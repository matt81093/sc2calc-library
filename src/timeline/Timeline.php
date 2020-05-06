<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\timeline;

use jc21\CliTable;
use holonet\sc2calc\Utils;
use holonet\sc2calc\job\Job;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\enum\Race;
use holonet\sc2calc\logic\Farm;
use holonet\sc2calc\sets\FarmSet;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\job\Dependency;
use holonet\sc2calc\logic\Hatchery;
use holonet\sc2calc\job\Availability;
use holonet\sc2calc\sets\HatcherySet;
use holonet\sc2calc\sets\IncomeSlots;
use holonet\sc2calc\logic\Spellcaster;
use holonet\sc2calc\mutation\Mutation;
use holonet\sc2calc\sets\SpellcasterSet;
use holonet\sc2calc\sets\ProductsManager;
use holonet\sc2calc\logic\ProductionQueue;
use Symfony\Component\Stopwatch\Stopwatch;
use holonet\sc2calc\init\ProtossInitialiser;
use holonet\sc2calc\sets\ProductionQueueSet;

/**
 * Timeline represents both the current state in the build order, and the
 * history of the jobs that have been handled.
 */
class Timeline {
	public const CHRONO_BOOST_HUMAN_DELAY = 0.1;

	public const CHRONO_BOOST_RATE = 1.5;

	/**
	 * @var Checkpoint[] $checkpoints List of unhandled checkpoints
	 */
	public $checkpoints = array();

	/**
	 * @var FarmSet $farms List of current farms
	 */
	public $farms;

	/**
	 * @var HatcherySet $hatcheries List of current hatcheries
	 */
	public $hatcheries;

	/**
	 * @var IncomeSlots $income List of current income slots
	 */
	public $income;

	/**
	 * @var array $options List of options for the timeline
	 */
	public $options;

	/**
	 * @var ProductionQueueSet $queues list of current production queues
	 */
	public $queues;

	/**
	 * @var Race $race race of the build order
	 */
	public $race;

	/**
	 * @var SpellcasterSet $spellcasters list of current spellcasters
	 */
	public $spellcasters;

	/**
	 * @var int $startupBuildDelay number of seconds to initially not build
	 */
	public $startupBuildDelay;

	/**
	 * @var Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	public $stopwatch;

	/**
	 * @var int $supplyCount current supply count
	 */
	public $supplyCount = 0;

	/**
	 * @var Event[] $_events list of events, representing handled jobs and checkpoints
	 */
	private $_events = array();

	/**
	 * @var ProductsManager $productManager Collection holding all the parsed product objects
	 */
	private $productManager;

	/**
	 * @param Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	public function __construct(Stopwatch $stopwatch, ProductsManager $productManager, Race $race, array $options = array()) {
		$this->productManager = $productManager;
		$this->stopwatch = $stopwatch;
		$this->race = $race;
		$this->options = $options;

		$this->startupBuildDelay = $options['startup build delay'] ?? 0;

		//instantiate collections
		$this->farms = new FarmSet();
		$this->hatcheries = new HatcherySet();
		$this->income = new IncomeSlots();
		$this->queues = new ProductionQueueSet();
		$this->spellcasters = new SpellcasterSet();
	}

	/**
	 * @return string representation of this timeline
	 */
	public function __toString(): string {
		$table = new CliTable();
		$table->addField('#', 'order');
		$table->addField('Started', 'started');
		$table->addField('Completed', 'completed');
		$table->addField('Supply', 'supply');
		if ($this->race->equals(Race::ZERG())) {
			$table->addField('Larvae', 'larvae');
		}
		$table->addField('Object', 'object');
		$table->addField('Minerals', 'minerals', false, 'cyan');
		$table->addField('Gas', 'gas', false, 'green');
		$table->addField('Energy', 'energy', false, 'yellow');

		$table->injectData($this->toArray());

		return $table->get();
	}

	/**
	 * @param Checkpoint $checkpoint to be added to the timeline
	 */
	public function addCheckpoint(Checkpoint $checkpoint): void {
		$this->checkpoints[] = $checkpoint;
		usort($this->checkpoints, array(Checkpoint::class, 'compare'));
	}

	/**
	 * Calculate time when the given job can be scheduled.
	 * @param Job[] $scheduledJobs
	 */
	public function calculate(Job $job, array $scheduledJobs): float {
		$this->stopwatch->start('Timeline::calculate');

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::calculate({$job})\n";
		}

		// start off optimistic
		$job->timeInitiated = 0;
		$job->timeStarted = (float)$this->startupBuildDelay;

		// trigger supply is not met
		if (isset($job->triggerSupply) && $job->triggerSupply !== $this->supplyCount) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Timeline::calculate(), trigger supply count is not met (needed {$job->triggerSupply} had {$this->supplyCount})\n";
			}
			$job->availability = new Availability(Availability::INSUFFICIENT_SUPPLY);
			$job->availability->supplyCount = $this->supplyCount;
			$job->availability->supplyNeeded = $job->triggerSupply;
			$job->timeStarted = INF;
			$this->stopwatch->stop('Timeline::calculate');

			return $job->timeStarted;
		}

		// when are dependencies met
		if (isset($job->dependency)) {
			$found = false;
			foreach ($scheduledJobs as $scheduledJob) {
				if ($scheduledJob === $job->dependency->job) {
					$found = true;

					break;
				}
			}
			if (!$found) {
				$job->availability = new Availability(Availability::MISSING_DEPENDENCY);
				$job->availability->missingDependency = $job->dependency->job;
				$job->timeStarted = INF;
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
			$job->timeStarted = Utils::floatmax($job->timeStarted, $job->dependency->type === Dependency::AT_START ? $job->dependency->job->timeStarted : $job->dependency->job->timeCompleted);
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Timeline::calculate(), dependency '{$job->dependency->job}' met at ".Utils::simple_time($job->timeStarted)."\n";
			}
		}

		// when are prerequisites met
		$prerequisites = $job->prerequisites();
		foreach ($prerequisites as $prerequisite) {
			// skip base
			if (in_array('Base', $prerequisite->types)) {
				continue;
			}

			// find earliest job to meet prerequisite
			$prerequisiteMet = INF;
			foreach ($scheduledJobs as $scheduledJob) {
				$productsCreated = $scheduledJob->productsCreated();
				if ($productsCreated !== null) {
					foreach ($productsCreated as $product) {
						if ($product !== null && $product->uid === $prerequisite->uid) {
							$prerequisiteMet = min($prerequisiteMet, $scheduledJob->timeCompleted);
						}
					}
				}
			}
			if ($prerequisiteMet === INF) {
				$job->availability = new Availability(Availability::MISSING_PREREQUISITE);
				$job->availability->missingPrerequisite = $prerequisite;
				$job->timeStarted = INF;
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
			$job->timeStarted = Utils::floatmax($job->timeStarted, $prerequisiteMet);
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Timeline::calculate(), '.count($prerequisites).' prerequisites met at '.Utils::simple_time($job->timeStarted)."\n";
		}

		// when is spellcaster available
		if ($job->energyCost() > 0) {
			$job->timeStarted = Utils::floatmax($job->timeStarted, $this->spellcasters->when($job->spellcasterTypeExpended(), $job->energyCost(), $job->tagsRequired));
			if (Sc2Calc::$DEBUG_PRINT) {
				echo 'Timeline::calculate(), spellcaster available at '.Utils::simple_time($job->timeStarted)."\n";
			}
			if ($job->timeStarted === INF) {
				$job->availability = new Availability(Availability::MISSING_SPELLCASTER);
				$job->availability->missingSpellcaster = $job->spellcasterTypeExpended();
				if (isset($job->tagsRequired)) {
					$job->availability->tagsRequired = $job->tagsRequired;
				}
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
		}

		if ($job->larvaCost() > 0) {
			$job->timeStarted = Utils::floatmax($job->timeStarted, $this->hatcheries->when($job->larvaCost(), $job->tagsRequired));
			if (Sc2Calc::$DEBUG_PRINT) {
				echo 'Timeline::calculate(), larva available at '.Utils::simple_time($job->timeStarted)."\n";
			}
			if ($job->timeStarted === INF) {
				$job->availability = new Availability(Availability::NO_LARVAE_PRODUCTION);
				if (isset($job->tagsRequired)) {
					$job->availability->tagsRequired = $job->tagsRequired;
				}
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
		}

		// when is supply capacity available
		if ($job->supplyCost(false) > 0) {
			$job->timeStarted = Utils::floatmax($job->timeStarted, $this->farms->when($this->supplyCount + $job->supplyCost(true)));
			if (Sc2Calc::$DEBUG_PRINT) {
				echo 'Timeline::calculate(), supply capacity available at '.Utils::simple_time($job->timeStarted)."\n";
			}
			if ($job->timeStarted === INF) {
				$job->availability = new Availability(Availability::INSUFFICIENT_SUPPLY_CAPACITY);
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
		}

		// when are production queues available
		list($queueTypesExpended, $expendAll) = $job->queueTypesExpended();
		if ($queueTypesExpended !== null) {
			list($time, $unavailableQueues) = $this->queues->when($queueTypesExpended, $expendAll, $job->tagsRequired);
			$job->timeStarted = Utils::floatmax($job->timeStarted, $time);
			if (Sc2Calc::$DEBUG_PRINT) {
				echo 'Timeline::calculate(), production queues available at '.Utils::simple_time($time)."\n";
			}
			if ($job->timeStarted === INF) {
				$job->availability = new Availability(Availability::MISSING_PRODUCTION_QUEUE);
				$job->availability->missingQueues = $unavailableQueues ?? array();
				if (isset($job->tagsRequired)) {
					$job->availability->tagsRequired = $job->tagsRequired;
				}
				$this->stopwatch->stop('Timeline::calculate');

				return $job->timeStarted;
			}
		}

		// for spawn larvae, delay until a hatchery is vomit-free
		$productsCreated = $job->productsCreated();
		if ($productsCreated !== null && count($productsCreated) > 0 && $productsCreated[0]->name === 'Spawn Larvae') {
			$job->timeStarted = Utils::floatmax($job->timeStarted, $this->hatcheries->whenVomit());
		}

		// delay transferring workers to gas until there is room for them
		$job->timeStarted = Utils::floatmax($job->timeStarted, $job->when($this->income));

		// when are there enough resources to send worker
		$initiateGas = isset($job->initiateGas) ? $job->initiateGas : $job->gasCost();
		$initiateMineral = isset($job->initiateMineral) ? $job->initiateMineral : $job->mineralCost();
		$job->timeInitiated = $this->income->when($initiateMineral, $initiateGas);
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::calculate(), Job {$job} can initiate".(isset($job->initiateMineral) ? (" when {$job->initiateMineral} minerals are available") : '').' at '.Utils::simple_time($job->timeInitiated)."\n";
		}

		// when are there enough resources to start building
		$gasCost = isset($job->triggerGas) ? max($job->triggerGas, $job->gasCost()) : $job->gasCost();
		$mineralCost = isset($job->triggerMineral) ? max($job->triggerMineral, $job->mineralCost()) : $job->mineralCost();
		if ($job->timeInitiated === INF) {
			$job->timeStarted = INF;
		} elseif (isset($job->initiateGas) || isset($job->initiateMineral)) {
			$income = clone $this->income;
			$mutation = new Mutation(-1);
			$mutation->time = $job->timeInitiated;
			$income->splice($mutation);
			$job->timeStarted = Utils::floatmax($job->timeStarted, $income->when($mineralCost, $gasCost));
		} else {
			$job->timeStarted = Utils::floatmax($job->timeStarted, $this->income->when($mineralCost, $gasCost));
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::calculate(), {$mineralCost} Minerals and {$gasCost} gas available at ".Utils::simple_time($job->timeStarted)."\n";
		}

		// no gas is being produced
		if ($job->timeStarted === INF) {
			$job->availability = new Availability(Availability::NO_GAS_PRODUCTION);
			$this->stopwatch->stop('Timeline::calculate');

			return $job->timeStarted;
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::calculate(), Job {$job} can start at ".Utils::simple_time($job->timeStarted)."\n";
		}
		$job->availability = new Availability(Availability::AVAILABLE);
		$this->stopwatch->stop('Timeline::calculate');

		return $job->timeStarted;
	}

	/**
	 * Determine if the job can be accommodated before the fixed job, without
	 * stalling the fixed job.
	 */
	public function canAccommodate(Job $job, Job $fixedJob): bool {
		// only jobs that could clash
		if ($job->larvaCost() === 0 && $job->energyCost() === 0 && $job->queueTypesExpended() === null) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Timeline::canAccommodate({$job}, {$fixedJob}): given job has no costs or queues => cannot clash\n";
			}

			return true;
		}

		if ($fixedJob->larvaCost() === 0 && $fixedJob->energyCost() === 0 && $fixedJob->queueTypesExpended() === null) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Timeline::canAccommodate({$job}, {$fixedJob}): fixed job has no costs or queues => cannot clash\n";
			}

			return true;
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::canAccommodate({$job}, {$fixedJob})\n";
		}

		// remember previous queues & spellcasters state
		$holdHatcheries = $this->hatcheries;
		$holdQueues = $this->queues;
		$holdSpellcasters = $this->spellcasters;
		$this->hatcheries = clone $this->hatcheries;
		$this->queues = clone $this->queues;
		$this->spellcasters = clone $this->spellcasters;

		// can we use larvae without delaying fixed job?
		if ($job->larvaCost() > 0) {
			$this->hatcheries->expend($job->timeStarted, $job->larvaCost(), $job->tagsRequired);
		}

		if ($fixedJob->larvaCost() > 0) {
			$larvaeAvailable = $this->hatcheries->when($fixedJob->larvaCost(), $fixedJob->tagsRequired);
		} else {
			$larvaeAvailable = -INF;
		}

		// can we use production queues without delaying fixed job?
		$this->queue($job, $job->timeStarted, true);
		list($queueTypesExpended, $expendAll) = $job->queueTypesExpended();
		if ($queueTypesExpended !== null) {
			list($queuesAvailable, $unused) = $this->queues->when($queueTypesExpended, $expendAll, $fixedJob->tagsRequired);
		} else {
			$queuesAvailable = -INF;
		}

		// can we use spellcaster without delaying fixed job?
		if ($job->energyCost() > 0) {
			$this->spellcasters->update($job->timeStarted);
			$this->spellcasters->expend($job->spellcasterTypeExpended(), $job->energyCost(), $job->timeStarted, $job->tagsRequired);
		}
		if ($fixedJob->energyCost() > 0) {
			$spellcasterAvailable = $this->spellcasters->when($fixedJob->spellcasterTypeExpended(), $fixedJob->energyCost(), $fixedJob->tagsRequired);
		} else {
			$spellcasterAvailable = -INF;
		}

		// reinstate remembered queues & spellcasters state
		$this->hatcheries = $holdHatcheries;
		$this->queues = $holdQueues;
		$this->spellcasters = $holdSpellcasters;

		return
			$larvaeAvailable <= $fixedJob->timeStarted &&
			$queuesAvailable <= $fixedJob->timeStarted &&
			$spellcasterAvailable <= $fixedJob->timeStarted;
	}

	/**
	 * Log an event.
	 */
	public function log(string $description, float $timeStarted, float $timeCompleted): void {
		$event = new Event();

		$event->description = $description;
		$energySurplus = $this->spellcasters->surplus(null, $timeStarted);
		foreach ($energySurplus as $energy) {
			$event->energySurplus[] = round($energy);
		}
		list($event->gasSurplus, $event->mineralSurplus) = $this->income->surplus($timeStarted);
		$event->larvae = $this->hatcheries->surplus($timeStarted);
		$event->order = count($this->_events);
		$event->supplyCapacity = $this->farms->surplus($timeStarted);
		$event->supplyCount = $this->supplyCount;
		$event->timeCompleted = $timeCompleted;
		$event->timeStarted = $timeStarted;

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Logging {$description}, supply capacity is {$event->supplyCapacity} at ".Utils::simple_time($timeStarted)."\n";
		}

		$this->_events[] = $event;
	}

	/**
	 * Process a single job, update the timeline accordingly, and handle all
	 * job-specific tasks.
	 */
	public function process(Job $job, bool $intheFuture = false): void {
		$this->stopwatch->start('Timeline::process');
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::process({$job}, ".($intheFuture ? 'true' : 'false').")\n";
		}

		// reset time completed
		$job->timeCompleted = INF;

		// handle mutations up to job start
		foreach ($job->mutations() as $mutation) {
			if ($mutation->time < $job->timeStarted) {
				$this->income->splice($mutation);
			}
		}

		if (!$intheFuture) {
			// handle checkpoints up to job start
			$this->processCheckpoints($job->timeStarted);
			// update all
			$this->update($job->timeStarted);
		}

		// expend resources
		$this->income->expend($job->mineralCost(), $job->gasCost());

		// when is job completed
		$job->timeCompleted = $job->timeStarted + $job->duration();

		// refund resources
		$this->income->expend(-$job->gasRefund(), 0);
		$this->income->expend(-$job->mineralRefund(), 0);

		// use production queues
		list($queueTypesExpended, $expendAll) = $job->queueTypesExpended();
		if ($queueTypesExpended !== null) {
			list($job->timeCompleted, $queues) = $this->queue($job);
		}

		// use energy
		if ($job->energyCost() > 0) {
			$this->spellcasters->expend($job->spellcasterTypeExpended(), $job->energyCost(), $job->timeStarted, $job->tagsRequired);
		}

		// special case: build is complete in 5 seconds when using a warpgate
		if (isset($queues) && count($queues) === 1) {
			if ($queues[0]->structure->name === 'Warpgate') {
				$job->timeCompleted = $job->timeStarted + 5;
			}
		}

		// use larva
		if ($job->larvaCost() > 0) {
			$this->hatcheries->expend($job->timeStarted, $job->larvaCost(), $job->tagsRequired);
		}

		// new products
		if ($job->productsCreated() !== null) {
			foreach ($job->productsCreated() as $product) {
				if ($product !== null) {

					// spawn larvae
					if ($product->name === 'Spawn Larvae') {
						$this->hatcheries->vomit($job->timeStarted);
					}

					// new hatchery
					if (in_array('Base', $product->types) && in_array('Zerg', $product->types)) {
						$this->hatcheries->add(new Hatchery($this->productManager->byIdentifier('SpawnLarvae'), $job->timeCompleted, 1, $job->tag));
					}

					// new spellcaster
					if (in_array('Spellcaster', $product->types)) {
						$this->spellcasters->add(new Spellcaster($product, $job->timeCompleted, $job->tag));
					}

					// new farm
					if ($product->supplyCapacity > 0) {
						$this->farms->add(new Farm($job->timeCompleted, $product->supplyCapacity));
					}
				}
			}
		}

		// destroy products
		if ($job->productsDestroyed() !== null) {
			foreach ($job->productsDestroyed() as $product) {

				// destroy spellcaster
				if (in_array('Spellcaster', $product->types)) {
					$this->spellcasters->remove($product, $job->timeCompleted);
				}

				// destroy farm
				if ($product->supplyCapacity > 0) {
					$this->farms->remove($product->supplyCapacity, $job->timeCompleted);
				}
			}
		}

		// process mutations
		foreach ($job->mutations() as $mutation) {
			if ($mutation->time >= $job->timeStarted) {
				$this->income->splice($mutation);
			}
		}

		// add or morph production queues
		$queueTypesCreated = $job->queueTypesCreated();
		if ($queueTypesCreated !== null) {
			if (isset($queues) && $job->morph()) {
				$this->queues->morph($queues, $job->timeStarted, $queueTypesCreated, $job->timeCompleted);
			} else {
				foreach ($queueTypesCreated as $queueType) {
					$this->queues->add(new ProductionQueue($queueType, $job->timeCompleted, $job->tag));
				}
			}
		}

		// create event
		if ($intheFuture) {
			$this->addCheckpoint(new Checkpoint($job->description(), $job->timeStarted, $job->timeCompleted));
		} else {
			$this->log($job->description(), $job->timeStarted, $job->timeCompleted);
		}

		// update supply count
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::process(), supply count = {$this->supplyCount} + {$job->supplyCost(false)}.\n";
		}
		$this->supplyCount += $job->supplyCost(false);
		$this->stopwatch->stop('Timeline::process');
	}

	/**
	 * Process checkpoints in chronological order up to given time.
	 * @param float $time
	 */
	public function processCheckpoints($time = INF): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Timeline::processCheckpoints('.Utils::simple_time($time).")\n";
		}
		foreach ($this->checkpoints as $key => $checkpoint) {
			if ($checkpoint->timeStarted <= $time) {
				$this->update($checkpoint->timeStarted);
				$this->log($checkpoint->description, $checkpoint->timeStarted, $checkpoint->timeCompleted);
				unset($this->checkpoints[$key]);
			}
		}
	}

	/**
	 * Calculate time when job would be completed, and expend production queues.
	 * @param float $time
	 * @param bool $tentative If true, chronoboosts will not be logged. Use this
	 *                        to perform dry runs of the queue use.
	 * @return array(int,array) First element is time job would be completed,
	 *                          second element is list of production queues used
	 */
	public function queue(Job $job, float $time = null, bool $tentative = false) {
		$ChronoBoost = $this->productManager->byIdentifier('ChronoBoost');
		$Nexus = $this->productManager->byIdentifier('Nexus');
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Timeline::queue({$job}, ".Utils::simple_time($time).', '.($tentative ? 'true' : 'false').")\n";
		}

		if ($time === null) {
			$time = $job->timeStarted;
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Timeline::queue(), job starts at '.Utils::simple_time($time)."\n";
		}

		// choose queues
		list($queueTypesExpended, $expendAll) = $job->queueTypesExpended();
		if ($queueTypesExpended !== null) {
			$queues = $this->queues->choose($time, $queueTypesExpended, $expendAll, $job->tagsRequired);
		}

		// build time
		$buildTime = $job->duration();
		if (isset($queues) && count($queues) === 1 && $queues[0]->structure->name === 'Warpgate') {
			$buildTime -= ProtossInitialiser::WARPGATE_QUEUE_REDUCTION;
		}

		// previous chrono boost overlaps this job
		if (isset($queues) && count($queues) === 1 && $queues[0]->chronoboosted + $ChronoBoost->timeCost > $time) {
			$boostTime = $queues[0]->chronoboosted;

			// calculate overlap with job
			$overlapStart = Utils::floatmax($boostTime, $time);
			$overlapEnd = min($boostTime + $ChronoBoost->timeCost * static::CHRONO_BOOST_RATE, $time + $buildTime);
			$overlap = Utils::floatmax(0, $overlapEnd - $overlapStart);

			// reduce build time
			$buildTime -= $overlap - $overlap / static::CHRONO_BOOST_RATE;
		}

		// chrono boosts
		if (isset($queues) && count($queues) === 1) {

			// process chronoboosts in an alternate reality
			$spellcasters = clone $this->spellcasters;
			for ($i = 0; $i < $job->chronoboost; $i++) {

				// start time of chrono boost
				$boostTime = Utils::floatmax($queues[0]->chronoboosted + $ChronoBoost->timeCost, $spellcasters->when($Nexus, $ChronoBoost->energyCost));
				if ($boostTime < $time + $buildTime) {
					$boostTime = Utils::floatmax($boostTime, $time + static::CHRONO_BOOST_HUMAN_DELAY);

					// calculate overlap with job
					$overlapStart = Utils::floatmax($boostTime, $time);
					$overlapEnd = min($boostTime + $ChronoBoost->timeCost * static::CHRONO_BOOST_RATE, $time + $buildTime);
					$overlap = Utils::floatmax(0, $overlapEnd - $overlapStart);

					// reduce build time
					$buildTime -= $overlap - $overlap / static::CHRONO_BOOST_RATE;

					// expend spellcasters
					$spellcasters->update($boostTime);
					$spellcasters->expend($Nexus, $ChronoBoost->energyCost, $boostTime);

					// log chronoboost & reserve energy
					if (!$tentative) {
						$this->addCheckpoint(new Checkpoint('CB: '.$job->description(), $boostTime, $boostTime + $ChronoBoost->timeCost));
						$spellcaster = $this->spellcasters->reserve($Nexus, $ChronoBoost->energyCost, $boostTime);
					}

					// queue is now chrono boosted
					$queues[0]->chronoboosted = $boostTime;
				}
			}
		}

		// build complete
		$completed = $time + $buildTime;

		// queue is now unavailable
		if (isset($queues)) {
			foreach ($queues as $queue) {
				$queue->busy($time, $completed, $job->busiesQueues());
			}

			return array($completed, $queues);
		}

		return array($completed, null);
	}

	/**
	 * Export the events of the timeline into a serialisable array.
	 */
	public function toArray(): array {
		$ret = array();
		foreach ($this->_events as $event) {
			$ret[$event->order] = $event->toArray();
		}
		ksort($ret);

		return $ret;
	}

	/**
	 * Update all things up to the given time.
	 */
	public function update(float $time): void {

		// update resources
		$this->income->update($time);

		// update hatcheries
		$this->hatcheries->update($time);

		// update spellcasters
		$this->spellcasters->update($time);

		// update production queues
		$this->queues->update($time);
	}

	/**
	 * Calculate time when job would be completed.
	 * @return array(int,array) First element is time job would be completed,
	 *                          second element is list of production queues used
	 */
	public function whenComplete(Job $job): array {

		// remember previous queues & spellcasters state
		$holdQueues = clone $this->queues;
		$holdSpellcasters = clone $this->spellcasters;

		// try to accomodate job before fixed job
		$result = $this->queue($job, $job->timeStarted, true);

		// reinstate remembered queues & spellcasters state
		$this->queues = $holdQueues;
		$this->spellcasters = $holdSpellcasters;

		return $result;
	}
}
