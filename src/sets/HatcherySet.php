<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use jc21\CliTable;
use LogicException;
use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\logic\Hatchery;

/**
 * The set of hatcheries available.
 */
class HatcherySet {
	/**
	 * @var Hatchery[] $_hatcheries List of hatcheries
	 */
	private $_hatcheries = array();

	/**
	 * @var bool
	 */
	private $_isClone = false;

	/**
	 * @var float $_lastUpdated Time when hatcheries were last updated
	 */
	private $_lastUpdated = 0;

	/**
	 * Create a copy of this.
	 */
	public function __clone() {
		$hatcheries = array();
		foreach ($this->_hatcheries as $hatchery) {
			$hatcheries[] = clone $hatchery;
		}
		$this->_hatcheries = $hatcheries;
		$this->_isClone = true;
	}

	public function __toString(): string {
		$cliTable = new CliTable();
		$cliTable->addField('#', 'order');
		$cliTable->addField('Created', 'created');
		$cliTable->addField('Larvae generated at', 'larva_timings');
		$cliTable->injectData($this->toArray());

		return $cliTable->get();
	}

	/**
	 * Add a hatchery to the list.
	 */
	public function add(Hatchery $hatchery): void {
		$this->_hatcheries[] = $hatchery;
		$hatchery->order = count($this->_hatcheries);
	}

	/**
	 * Use up a single larva from any hatchery that has one available and that
	 * has the required tags at the given time.
	 * @param array $tagsRequired
	 */
	public function expend(float $time, int $larvae, $tagsRequired = null): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '').'Hatcheries::expend('.Utils::simple_time($time).")\n";
		}

		// update all
		$this->update($time);
		for ($i = 0; $i < $larvae; $i++) {
			// choose hatchery
			foreach ($this->select($tagsRequired) as $hatchery) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo($this->_isClone ? 'lon ' : '').'Hatcheries::expend(), hatchery created at '.Utils::simple_time($hatchery->created)." has {$hatchery->larvae} larvae.\n";
				}
				if ($hatchery->larvae > 0 && $hatchery->created <= $time) {
					if (!isset($candidate)) {
						$candidate = $hatchery;
					} elseif ($hatchery->larvae > $candidate->larvae) {
						$candidate = $hatchery;
					} elseif ($hatchery->larvae === $candidate->larvae && $hatchery->nextVomit() < $candidate->nextVomit()) {
						$candidate = $hatchery;
					} elseif ($hatchery->larvae === $candidate->larvae && $hatchery->nextVomit() === $candidate->nextVomit() && $hatchery->nextLarvaGenerated < $candidate->nextLarvaGenerated) {
						$candidate = $hatchery;
					}
				}
			}
			if (!isset($candidate)) {
				throw new LogicException('No hatcheries have larvae available at '.Utils::simple_time($time).'.');
			}

			// reset time next larva is generated
			if ($candidate->larvae === 3) {
				$candidate->nextLarvaGenerated = $time + Hatchery::LARVA_TIME - $candidate->timeRebate;
				if (Sc2Calc::$DEBUG_PRINT) {
					echo($this->_isClone ? 'lon ' : '').'Hatcheries::expend(), at '.Utils::simple_time($time)." we drop below 3 larvae. The rebate is {$candidate->timeRebate} seconds, so the next larva is generated at ".Utils::simple_time($candidate->nextLarvaGenerated)."\n";
				}
			}

			// use larva
			if (Sc2Calc::$DEBUG_PRINT) {
				echo($this->_isClone ? 'lon ' : '')."Hatcheries[{$candidate->order}]::larvae-- = ".($candidate->larvae - 1)."\n";
			}
			$candidate->larvae--;
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '')."Hatcheries::expend(), done!\n";
		}
	}

	/**
	 * Find all hatcheries with one of the given tags.
	 * @param string[] $tagsRequired
	 * @return Hatchery[] Array of references to the hatcheries
	 */
	public function select(array $tagsRequired = null) {
		$hatcheries = array();
		foreach ($this->_hatcheries as $hatchery) {
			if ($tagsRequired === null || (isset($hatchery->tag) && in_array($hatchery->tag, $tagsRequired))) {
				$hatcheries[] = $hatchery;
			}
		}

		return $hatcheries;
	}

	/**
	 * Calculate surplus numbers of larvae on all hatcheries that have the
	 * required tags at a given time in the future.
	 * @param string[] $tagsRequired
	 * @return int[]
	 */
	public function surplus(float $time, array $tagsRequired = null): array {
		$larvae = array();
		foreach ($this->select($tagsRequired) as $hatchery) {
			if ($hatchery->created <= $time) {
				$larvae[] = $hatchery->surplus($time);
			}
		}

		return $larvae;
	}

	/**
	 * Export the income slots into a serialisable array.
	 */
	public function toArray(): array {
		$ret = array();
		foreach ($this->_hatcheries as $hatchery) {
			$ret[] = array(
				'order' => $hatchery->order,
				'created' => Utils::simple_time($hatchery->created),
				'larva_timings' => $hatchery->getLarvaTimings(),
			);
		}

		return $ret;
	}

	/**
	 * Update the state of all hatcheries up to given time.
	 */
	public function update(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '').'Hatcheries::update('.Utils::simple_time($time).")\n";
		}

		if ($time < $this->_lastUpdated) {
			throw new LogicException('Cannot generate larvae in the past.');
		}

		// generate larvae
		foreach ($this->_hatcheries as $hatchery) {
			$hatchery->update($time);
		}

		$this->_lastUpdated = $time;
	}

	/**
	 * Queue use of vomit at given time in the future.
	 */
	public function vomit(float $time): void {
		$candidate = null;
		// choose hatchery
		foreach ($this->_hatcheries as $hatchery) {
			if ($hatchery->created <= $time) {
				if (!isset($candidate)) {
					$candidate = $hatchery;
				} elseif ($hatchery->whenVomit() < $candidate->whenVomit()) {
					$candidate = $hatchery;
				}
			}
		}

		if ($candidate === null) {
			throw new LogicException('Could not find candidate to vomit to');
		}

		// queue vomit
		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '').'Hatcheries::vomit(), vomitting to hatchery created at '.Utils::simple_time($candidate->created);
		}

		$candidate->vomit($time);
	}

	/**
	 * Calculate time when hatcheries with the required tags has the given
	 * number of free larva.
	 * @param string[] $tagsRequired
	 * @return float time / null if never
	 */
	public function when(int $larvae, array $tagsRequired = null): float {
		$time = null;
		if ($larvae === 1) {
			foreach ($this->select($tagsRequired) as $hatchery) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo($this->_isClone ? 'lon ' : '').'Hatcheries['.$hatchery->order.']::when()='.Utils::simple_time($hatchery->when()).', has '.$hatchery->larvae." larvae.\n";
				}
				$when = $hatchery->when();
				if ($time === null || $time > $when) {
					$time = $when;
				}
			}
		} else {
			$hatcheries = clone $this;
			for ($i = 0; $i < $larvae; $i++) {
				$time = $hatcheries->when(1, $tagsRequired);
				$hatcheries->expend($time, 1, $tagsRequired);
			}
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '').'Hatcheries::when(), returns '.Utils::simple_time($time)."\n";
		}

		return $time ?? INF;
	}

	/**
	 * @return float calculated time when another vomit can be queued on any hatchery
	 */
	public function whenVomit(): float {
		$time = null;
		foreach ($this->_hatcheries as $hatchery) {
			$when = $hatchery->whenVomit();
			if ($time === null || $time > $when) {
				$time = $when;
			}
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo($this->_isClone ? 'lon ' : '').'Hatcheries::whenVomit(), returns '.Utils::simple_time($time)."\n";
		}

		return $time ?? INF;
	}
}
