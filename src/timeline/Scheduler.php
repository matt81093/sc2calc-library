<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\timeline;

use RuntimeException;
use holonet\sc2calc\Utils;
use holonet\sc2calc\job\Job;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\job\Availability;
use Symfony\Component\Stopwatch\Stopwatch;
use holonet\sc2calc\error\InvalidBuildException;

/**
 * The scheduler is responsible for planning the timing of the jobs that
 * constitute the build order. It schedules the jobs with fixed triggers in the
 * order in which they appear, and all other jobs wherever possible.
 */
class Scheduler {
	/**
	 * @var array<int, Job> $_fixedJobs Array of unscheduled fixed jobs
	 */
	private $_fixedJobs;

	/**
	 * @var Job[] $_floatingJobs Array of unscheduled non-recurring floating jobs
	 */
	private $_floatingJobs;

	/**
	 * @var Job[] $_recurringJobs Array of recurring floating jobs
	 */
	private $_recurringJobs;

	/**
	 * @var Job[] $_scheduledJobs Array of scheduled jobs
	 */
	private $_scheduledJobs;

	/**
	 * @var Timeline $_timeline Timeline to schedule on
	 */
	private $_timeline;

	/**
	 * @var Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	private $stopwatch;

	/**
	 * Initialize the scheduler.
	 * @param Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 * @param Timeline $timeline Timeline to schedule jobs on
	 * @param Job[] $unscheduledJobs Jobs to be scheduled
	 */
	public function __construct(Stopwatch $stopwatch, Timeline $timeline, array $unscheduledJobs) {
		$this->stopwatch = $stopwatch;
		$this->_timeline = $timeline;
		$this->_scheduledJobs = array();
		$this->_fixedJobs = array();
		$this->_floatingJobs = array();
		$this->_recurringJobs = array();
		foreach ($unscheduledJobs as $job) {
			if (isset($job->triggerGas) || isset($job->triggerSupply) || isset($job->triggerMineral)) {
				$this->_fixedJobs[] = $job;
			} elseif ($job->recurring) {
				$this->_recurringJobs[] = $job;
			} else {
				$this->_floatingJobs[] = $job;
			}
		}
	}

	/**
	 * Schedule all jobs.
	 * @return array Scheduled jobs, ordered by start time
	 */
	public function schedule() {
		$this->stopwatch->start('Scheduler::schedule');
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Scheduler::schedule(), scheduling '.count($this->_fixedJobs).' fixed jobs, '.count($this->_floatingJobs).' floating jobs, '.count($this->_recurringJobs)." recurring jobs.\n";
		}

		// process fixed jobs
		foreach ($this->_fixedJobs as $i => $job) {

			// squeeze in non-recurring floating jobs until fixed job is available
			do {
				$this->_timeline->calculate($job, $this->_scheduledJobs);
			} while ($job->timeStarted === INF && !$this->deadEnd($job) && $this->squeeze($job));

			// fixed job could not be scheduled
			if ($job->timeStarted === INF) {
				throw $this->reportUnavailable(array($job));
			}

			/**
			 * If this job has no supply requirement, estimate the proper supply
			 * requirement to prevent squeezing in floating jobs that would make
			 * the next job with a supply requirement impossible.
			 */
			if (!isset($job->triggerSupply)) {
				$supplyDelta = 0;
				$triggerSupply = 0;
				for ($j = $i; $j < count($this->_fixedJobs); $j++) {
					if (isset($this->_fixedJobs[$j]->triggerSupply)) {
						$triggerSupply = $this->_fixedJobs[$j]->triggerSupply;

						break;
					}
					$supplyDelta += $this->_fixedJobs[$j]->supplyCost(false);
				}

				$job->triggerSupply = $triggerSupply - $supplyDelta;
			}

			// squeeze in any floating jobs that will fit
			while ($this->squeeze($job)) {
				$this->_timeline->calculate($job, $this->_scheduledJobs);
				if ($job->timeStarted === INF) {
					throw $this->reportUnavailable(array($job));
				}
			}

			// process fixed job
			$this->process($job);
		}

		// process remaining floating jobs
		while (count($this->_floatingJobs) > 0) {

			// pick earliest available non-recurring job
			$job = $this->earliest($this->_floatingJobs);
			if (!isset($job)) {
				throw $this->reportUnavailable($this->_floatingJobs);
			}

			// squeeze in some recurring floating jobs
			while ($this->squeeze($job, true)) {
				$this->_timeline->calculate($job, $this->_scheduledJobs);
				if ($job->timeStarted === INF) {
					throw $this->reportUnavailable(array($job));
				}
			}
			$this->process($job);
		}

		// process remaining checkpoints
		$this->_timeline->processCheckpoints();

		$this->stopwatch->stop('Scheduler::schedule');

		return $this->_scheduledJobs;
	}

	/**
	 * @param bool $recurring
	 * @return Job[]
	 */
	private function candidates(bool $recurring = null): array {

		// choose candidates
		if ($recurring === null) {
			$candidates = array_merge($this->_floatingJobs, $this->_recurringJobs);
		} elseif ($recurring) {
			$candidates = $this->_recurringJobs;
		} else {
			$candidates = $this->_floatingJobs;
		}

		// calculate all candidates
		// ignore candidates that are not available ever
		foreach ($candidates as $key => $job) {
			$this->_timeline->calculate($job, $this->_scheduledJobs);
			if ($job->timeStarted === INF) {
				unset($candidates[$key]);
			}
		}

		return $candidates;
	}

	/**
	 * Determine if job could be solved by squeezing in some floating jobs. Note
	 * that at present, this will always return false if there are non-recurring
	 * floating jobs remaining.
	 * @param Job $job
	 */
	private function deadEnd($job): bool {
		$candidates = $this->candidates();
		foreach ($candidates as $candidate) {
			if (!$candidate->recurring || $job->availability->solvedBy($candidate)) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo "Scheduler::deadEnd({$job}), job reports '{$job->availability}'. But it is not a dead end, because of job '{$candidate}'\n";
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * Get earliest available job from the given jobs.
	 * @param Job[] $jobs jobs to choose from
	 * @param bool $recurring if set, only either recurring or non-recurring jobs are considered
	 * @return Job|null Chosen job or null if n / a
	 */
	private function earliest(array $jobs, bool $recurring = null): ?Job {
		$candidate = null;
		foreach ($jobs as $job) {
			if ($recurring === null || $job->recurring === $recurring) {
				$this->_timeline->calculate($job, $this->_scheduledJobs);
				if ($job->timeStarted !== INF) {
					if ($candidate === null || $job->timeStarted < $candidate->timeStarted) {
						$candidate = $job;
					}
				}
			}
		}

		return $candidate;
	}

	/**
	 * Process a job, remove it from unscheduled jobs, and process all mutations
	 * that are available afterwards.
	 */
	private function process(Job $job): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Scheduler::process({$job}), job starts at ".Utils::simple_time($job->timeStarted)."\n";
		}

		// move to scheduled jobs
		$job->pickOrder = count($this->_scheduledJobs);
		$this->_scheduledJobs[] = $job;
		if (($key = array_search($job, $this->_fixedJobs)) !== false) {
			unset($this->_fixedJobs[$key]);
		}
		if (($key = array_search($job, $this->_floatingJobs)) !== false) {
			unset($this->_floatingJobs[$key]);
		}
		if (($key = array_search($job, $this->_recurringJobs)) !== false) {
			unset($this->_recurringJobs[$key]);
		}

		// update timeline
		$this->_timeline->process($job);

		// cancel recurring jobs
		$job->cancel($this->_recurringJobs);

		// process any available non-consumptive jobs
		foreach ($this->_floatingJobs as $job) {
			if (!$job->consumptive()) {
				$this->_timeline->calculate($job, $this->_scheduledJobs);
				if ($job->timeStarted !== INF) {
					$this->_timeline->process($job, true);
					$job->pickOrder = count($this->_scheduledJobs);
					$this->_scheduledJobs[] = $job;
					if (($key = array_search($job, $this->_floatingJobs)) !== false) {
						unset($this->_floatingJobs[$key]);
					}
				}
			}
		}
	}

	/**
	 * Throw an error, reporting all of the given jobs that are unavailable,
	 * except those that are dependant on another unavailable job.
	 * @param Job[] $jobs
	 * @return InvalidBuildException with a description on why the job is unavailable
	 */
	private function reportUnavailable(array $jobs): InvalidBuildException {
		$error = '';
		$description = '';
		foreach ($jobs as $job) {
			$this->_timeline->calculate($job, $this->_scheduledJobs);
			if ($job->availability->status !== Availability::MISSING_DEPENDENCY && $job->availability->status !== Availability::AVAILABLE) {
				$error .= "Job '{$job}' could not be scheduled. {$job->availability}";
				$description .= $job->availability->description();
			}
		}

		return new InvalidBuildException($error, $description);
	}

	/**
	 * Schedule as a floating job that can be squeezed in without delaying the
	 * fixed job. If there is supply gap before the fixed job, a floating
	 * jobs is scheduled as needed to bridge the gap, possibly delaying the
	 * fixed job.
	 * @param Job $fixedJob Fixed job
	 * @param bool $recurring if set, consider either only recurring or non-recurring jobs
	 * @return bool true, if a job could be squeezed in
	 */
	private function squeeze(Job $fixedJob, bool $recurring = null): bool {
		$this->stopwatch->start('Scheduler::squeeze');
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Scheduler::squeeze({$fixedJob}, ".($recurring === null ? 'null' : ($recurring ? 'true' : 'false')).")\n";
		}

		// squeezing is mandatory if the fixed job is unavailable
		$mandatory = $fixedJob->availability->status !== Availability::AVAILABLE;
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Scheduler::squeeze(), squeezing is '.($mandatory ? '' : 'not ')."mandatory!\n";
		}

		// choose candidates
		if ($recurring === null) {
			$candidates = array_merge($this->_floatingJobs, $this->_recurringJobs);
		} elseif ($recurring) {
			$candidates = $this->_recurringJobs;
		} else {
			$candidates = $this->_floatingJobs;
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Scheduler::squeeze(), choosing from '.count($candidates)." candidates!\n";
		}

		// ignore recurring candidates that build the same product as the fixed job
		if (!$mandatory && $fixedJob->productBuilt() !== null) {
			foreach ($candidates as $key => $job) {
				$fixedJobProduct = $fixedJob->productBuilt();
				$jobProduct = $job->productBuilt();

				if ($job->recurring && $jobProduct !== null && $fixedJobProduct !== null && $jobProduct->uid === $fixedJobProduct->uid) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), eliminating {$job}, which builds the same product as {$fixedJob} '({$jobProduct->name})'.\n";
					}
					unset($candidates[$key]);
				}
			}
		}

		// ignore candidates that are not available before fixed job starts
		// if mandatory, instead ignore candidates that are not available ever
		foreach ($candidates as $key => $job) {
			$this->_timeline->calculate($job, $this->_scheduledJobs);
			if (!$mandatory && $job->timeStarted > $fixedJob->timeStarted) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo "Scheduler::squeeze(), eliminating {$job}, which is available at ".Utils::simple_time($job->timeStarted).', but fixed job starts at '.Utils::simple_time($fixedJob->timeStarted)."\n";
				}
				unset($candidates[$key]);
			} elseif ($mandatory && $job->timeStarted === INF) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo "Scheduler::squeeze(), eliminating {$job}, which is unavailable because ".$job->availability."\n";
				}
				unset($candidates[$key]);
			}
		}

		// ignore jobs that affect supply the wrong way
		if (isset($fixedJob->triggerSupply)) {
			$supplyGap = $fixedJob->triggerSupply - $this->_timeline->supplyCount;
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Scheduler::squeeze(), supply gap is {$supplyGap}, current supply count is {$this->_timeline->supplyCount}, fixed job is triggered at {$fixedJob->triggerSupply}\n";
			}
			foreach ($candidates as $key => $job) {
				if ($supplyGap === 0 && $job->supplyCost(true) !== 0) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), eliminating {$job}; Supply gap is {$supplyGap}, job's supply cost is {$job->supplyCost(true)}\n";
					}
					unset($candidates[$key]);
				} elseif ($supplyGap > 0 && ($job->supplyCost(true) > $supplyGap || $job->supplyCost(true) < 0)) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), #3 Eliminating {$job}\n";
					}
					unset($candidates[$key]);
				} elseif ($supplyGap < 0 && ($job->supplyCost(true) < $supplyGap || $job->supplyCost(true) > 0)) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), #4 Eliminating {$job}\n";
					}
					unset($candidates[$key]);
				}
			}
		}

		// if not mandatory, ignore jobs that exceed surplus minerals or gas
		if (!$mandatory) {
			foreach ($candidates as $key => $job) {

				// always allow jobs that don't cost resources
				if ($job->mineralCost() === 0 && $job->gasCost() === 0) {
					continue;
				}

				// the job affects income
				$mutations = $job->mutations();
				$mutations->sort();
				if (count($mutations) > 0) {

					// calculate job start & complete time
					$jobComplete = $this->_timeline->whenComplete($job);

					// set up alternate reality income
					$income = clone $this->_timeline->income;
					foreach ($mutations as $mutation) {
						$income->splice($mutation);
					}

					// the job does not affect income
				} else {
					$income = $this->_timeline->income;
				}

				// if surplus is not great enough
				list($gasSurplus, $mineralSurplus) = $income->surplus($fixedJob->timeStarted);
				if (round($gasSurplus) < $job->gasCost() + $fixedJob->gasCost()) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), eliminating {$job}. Gas needed for both jobs is ".
						($job->gasCost() + $fixedJob->gasCost()).", gas surplus is {$gasSurplus}.\n";
					}
					unset($candidates[$key]);
				}
				if (round($mineralSurplus) < $job->mineralCost() + $fixedJob->mineralCost()) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), eliminating {$job}. Mineral needed for both jobs is ".
						($job->mineralCost() + $fixedJob->mineralCost()).", mineral surplus is {$mineralSurplus}.\n";
					}
					unset($candidates[$key]);
				}
			}
		}

		// if not mandatory, ignore jobs whose larvae, production queue
		// or spellcaster usage would stall fixed job
		if (!$mandatory) {
			foreach ($candidates as $key => $job) {
				if (!$this->_timeline->canAccommodate($job, $fixedJob)) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), #16 Eliminating {$job}\n";
					}
					unset($candidates[$key]);
				}
			}
		}

		// ignore candidates that exceed supply gap
		$supplyGap = $this->supplyGap();
		foreach ($candidates as $key => $job) {
			if ($supplyGap >= 0 && $job->supplyCost(true) > $supplyGap) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo "Scheduler::squeeze(), #9 Eliminating {$job}\n";
				}
				unset($candidates[$key]);
			}
		}

		// ignore jobs that cause fixed job to exceed supply capacity
		foreach ($candidates as $job) {
			if ($job->supplyCost(true) > 0) {

				// how much supply capacity is needed
				$supplyNeeded = $this->_timeline->supplyCount + $fixedJob->supplyCost(true) + $job->supplyCost(true);

				// discard candidate if supply capacity is not available
				$time = $this->_timeline->farms->when($supplyNeeded);
				if (!$mandatory && $time > $fixedJob->timeStarted) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), #14 Eliminating {$job}\n";
					}
					unset($candidates[$key]);
				} elseif ($mandatory && $time === INF) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo "Scheduler::squeeze(), #15 Eliminating {$job}\n";
					}
					unset($candidates[$key]);
				}
			}
		}

		// no floating jobs are available
		if (count($candidates) === 0) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Scheduler::squeeze(), all jobs were eliminated!\n";
			}

			// if mandatory, throw an error
			if ($mandatory) {
				throw $this->reportUnavailable(array($fixedJob));
			}

			$this->stopwatch->stop('Scheduler::squeeze');

			return false;
		}

		// choose earliest available job
		$job = $this->earliest($candidates);

		if ($job === null) {
			throw new RuntimeException('Could not determine earliest of a set of jobs');
		}

		// process floating job
		if (Sc2Calc::$DEBUG_PRINT) {
			$report = '';
			foreach ($candidates as $candidate) {
				$report .= (isset($notFirst) ? ', ' : '').$candidate.'('.Utils::simple_time($candidate->timeStarted).')';
				$notFirst = true;
			}
			echo "Scheduler::squeeze(), remaining candidates are {$report}\n";
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Scheduler::squeeze(), chosen {$job}\n";
		}
		$this->process($job);

		// reschedule, if recurring
		if ($job->recurring) {
			$this->_recurringJobs[] = clone $job;
		}
		$this->stopwatch->stop('Scheduler::squeeze');

		return true;
	}

	/**
	 * Gap between current supply count and supply trigger of next fixed
	 * job that has one.
	 * @return int supply gap
	 */
	private function supplyGap(): int {
		foreach ($this->_fixedJobs as $job) {
			if (isset($job->triggerSupply)) {
				return $job->triggerSupply - $this->_timeline->supplyCount;
			}
		}

		return PHP_INT_MAX;
	}
}
