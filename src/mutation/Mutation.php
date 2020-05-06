<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\mutation;

use holonet\sc2calc\logic\IncomeSlot;
use holonet\sc2calc\sets\IncomeSlots;
use holonet\sc2calc\error\InvalidBuildException;

/**
 * Mutation of the income, used to handle probe transfers, scouting, temporary
 * unavailability of workers, etc.
 */
class Mutation {
	/**
	 * @var float $delay delay between negative and positive effect of mutation
	 */
	public $delay;

	/**
	 * @var int $gasChange number of workers put on or taken off gas
	 */
	public $gasChange;

	/**
	 * @var int $mineralChange number of workers put on or taken off mineral
	 */
	public $mineralChange;

	/**
	 * @var float $time time at which this mutation occurs
	 */
	public $time;

	/**
	 * @var array $_gasNegativeChange workers taken off gas, specified per geyser
	 */
	protected $_gasNegativeChange;

	/**
	 * @var array $_gasPositiveChange workers put on gas, specified per geyser
	 */
	protected $_gasPositiveChange;

	/**
	 * @var array $_mineralNegativeChange workers taken off minerals, specified per base
	 */
	protected $_mineralNegativeChange;

	/**
	 * @var array $_mineralPositiveChange workers put on minerals, specified per base
	 */
	protected $_mineralPositiveChange;

	public function __construct(int $mineralChange = 0, int $gasChange = 0) {
		$this->mineralChange = $mineralChange;
		$this->gasChange = $gasChange;
	}

	/**
	 * @return string representation of this Mutation
	 */
	public function __toString(): string {
		$changes = array();

		// describe transfer from mineral to gas or vice versa
		if ($this->gasChange === -$this->mineralChange) {
			if ($this->gasChange > 0) {
				return "Transfer {$this->gasChange} workers to gas";
			}

			return "Transfer {$this->mineralChange} workers to minerals";
		}

		// describe gas change
		if (isset($this->_gasNegativeChange, $this->_gasPositiveChange)) {
			foreach ($this->_gasNegativeChange as $i => $change) {
				if ($change !== 0) {
					$changes[] = '-'.$change.' workers on '.(count($this->_gasNegativeChange) > 1 ? (' on geyser #'.$i) : 'gas');
				}
			}
			foreach ($this->_gasPositiveChange as $i => $change) {
				if ($change !== 0) {
					$changes[] = '+'.$change.' workers on '.(count($this->_gasPositiveChange) > 1 ? (' on geyser #'.$i) : 'gas');
				}
			}
		} elseif (is_int($this->gasChange)) {
			$changes[] = ($this->gasChange > 0 ? '+' : '').$this->gasChange.' workers on gas';
		}

		// describe mineral change
		if (isset($this->_mineralNegativeChange, $this->_mineralPositiveChange)) {
			foreach ($this->_mineralNegativeChange as $i => $change) {
				if ($change !== 0) {
					$changes[] = '-'.$change.' workers on '.(count($this->_mineralNegativeChange) > 1 ? (' on geyser #'.$i) : 'minerals');
				}
			}
			foreach ($this->_mineralPositiveChange as $i => $change) {
				if ($change !== 0) {
					$changes[] = '+'.$change.' workers on '.(count($this->_mineralPositiveChange) > 1 ? (' on geyser #'.$i) : 'minerals');
				}
			}
		} elseif (is_int($this->mineralChange)) {
			$changes[] = ($this->mineralChange > 0 ? '+' : '').$this->mineralChange.' workers on minerals';
		}

		return implode(', ', $changes);
	}

	/**
	 * Apply this mutation to given income slot.
	 */
	public function apply(IncomeSlot $slot): void {
		$this->applyNegative($slot);
		$this->applyPositive($slot);
	}

	/**
	 * Apply negative effect of this mutation to given income slot.
	 */
	public function applyNegative(IncomeSlot $slot): void {
		$this->distribute($slot);
		if (isset($this->_gasNegativeChange)) {
			for ($i = 0; $i < count($this->_gasNegativeChange); $i++) {
				if (($slot->gasMiners[$i] += $this->_gasNegativeChange[$i]) < 0) {
					throw new InvalidBuildException('Attempting to take workers off gas where there were none.',
						"Your build order contains a job that takes workers off gas, but you either didn't put workers on gas, or are trying to take more workers off gas than were put on. Most likely, the job that takes workers off gas was placed earlier in the queue than the job that puts the workers on gas. Ensure that the job that takes workers off gas must be queued later, for example by writing '@100 gas take 3 off gas'."
					);
				}
			}
		}

		if (isset($this->_mineralNegativeChange)) {
			for ($i = 0; $i < count($this->_mineralNegativeChange); $i++) {
				if (($slot->mineralMiners[$i] += $this->_mineralNegativeChange[$i]) < 0) {
					throw new InvalidBuildException('Attempting to take workers off minerals where there were none.',
						"Either you're trying to put more workers on gas, or transfer more workers to a new Nexus than you have workers available."
					);
				}
			}
		}
	}

	/**
	 * Apply positive effect of this mutation to given income slot.
	 */
	public function applyPositive(IncomeSlot $slot): void {
		if (isset($this->_gasPositiveChange)) {
			for ($i = 0; $i < count($this->_gasPositiveChange); $i++) {
				$slot->gasMiners[$i] += $this->_gasPositiveChange[$i];
			}
		}

		if (isset($this->_mineralPositiveChange)) {
			for ($i = 0; $i < count($this->_mineralPositiveChange); $i++) {
				$slot->mineralMiners[$i] += $this->_mineralPositiveChange[$i];
			}
		}
	}

	/**
	 * Compare two mutations by time.
	 */
	public static function compare(self $mutation1, self $mutation2): int {
		return $mutation1->time <=> $mutation2->time;
	}

	/**
	 * Choose distribution of workers taken off or put on resources per geyser
	 * or base, based on the given income slot. This distribution is then
	 * solidified, and applied to every subsequent income slot.
	 */
	public function distribute(IncomeSlot $slot): void {

		// don't distribute twice
		if (isset($this->_gasNegativeChange) || isset($this->_mineralNegativeChange)) {
			return;
		}

		// auto-distribute miners on gas
		if (!empty($this->gasChange)) {

			// single geyser
			if (count($slot->gasMiners) === 0) {
				throw new InvalidBuildException("You don't have any geysers!",
					"Workers can only be transferred to an Assimilator after the Assimilator has been completed. You can set this up by writing '13 Assimilator > put 3 on gas'."
				);

				// single geyser
			}
			if (count($slot->gasMiners) === 1) {
				$gasChange = array($this->gasChange);

			// multiple geysers
			} else {
				$gasChange = array_fill(0, count($slot->gasMiners), 0);

				// take miners off gas
				if ($this->gasChange < 0) {
					$left = -$this->gasChange;
					do {
						$mostSaturated = 0;
						for ($i = 1; $i < count($slot->gasMiners); $i++) {
							if ($slot->gasMiners[$i] + $gasChange[$i] > $slot->gasMiners[$mostSaturated] + $gasChange[$mostSaturated]) {
								$mostSaturated = $i;
							}
						}
						$gasChange[$mostSaturated]--;
					} while (--$left > 0);

				// put miners on gas
				} else {
					$left = $this->gasChange;
					do {
						$leastSaturated = 0;
						for ($i = 1; $i < count($slot->gasMiners); $i++) {
							if ($slot->gasMiners[$i] + $gasChange[$i] < $slot->gasMiners[$leastSaturated] + $gasChange[$leastSaturated]) {
								$leastSaturated = $i;
							}
						}
						$gasChange[$leastSaturated]++;
					} while (--$left > 0);
				}
			}

			// store changes
			$this->storeGasChanges($gasChange);
		}

		// auto-distribute miners on minerals
		if (!empty($this->mineralChange)) {

			// single base
			if (count($slot->mineralMiners) === 1) {
				$mineralChange = array($this->mineralChange);

			// multiple bases
			} else {
				$mineralChange = array_fill(0, count($slot->mineralMiners), 0);

				// take miners off minerals
				if ($this->mineralChange < 0) {
					$left = -$this->mineralChange;
					do {
						$mostSaturated = 0;
						for ($i = 1; $i < count($slot->mineralMiners); $i++) {
							if ($slot->mineralMiners[$i] + $mineralChange[$i] > $slot->mineralMiners[$mostSaturated] + $mineralChange[$mostSaturated]) {
								$mostSaturated = $i;
							}
						}
						$mineralChange[$mostSaturated]--;
					} while (--$left > 0);

				// put miners on minerals
				} else {
					$left = $this->mineralChange;
					do {
						$leastSaturated = 0;
						for ($i = 1; $i < count($slot->mineralMiners); $i++) {
							if ($slot->basesOperational[$i]) {
								if ($slot->mineralMiners[$i] + $mineralChange[$i] < $slot->mineralMiners[$leastSaturated] + $mineralChange[$leastSaturated]) {
									$leastSaturated = $i;
								}
							}
						}
						$mineralChange[$leastSaturated]++;
					} while (--$left > 0);
				}
			}

			// store changes
			$this->storeMineralChanges($mineralChange);
		}
	}

	/**
	 * Calculate the earliest time when this mutation could be applied to the
	 * given income slots, after the given time.
	 * @return float
	 */
	public function when(float $time, IncomeSlots $income): ?float {
		//if putting drones on gas, delay until there is a geyser with <3 available
		if ($this->gasChange > 0) {
			for ($i = 0; $i < count($income); $i++) {
				$slot = $income[$i];
				if ($slot->endTime > $time) {
					for ($j = 0; $j < count($slot->gasMiners); $j++) {
						if ($slot->geysersOperational[$j] && $slot->gasMiners[$j] < 3) {
							return $slot->startTime;
						}
					}
				}
			}

			return INF;
		}

		return null;
	}

	/**
	 * @param array $gasChange Array of changes to save
	 */
	protected function storeGasChanges(array $gasChange): void {
		$this->_gasNegativeChange = array();
		$this->_gasPositiveChange = array();
		foreach ($gasChange as $change) {
			$this->_gasNegativeChange[] = $change < 0 ? $change : 0;
			$this->_gasPositiveChange[] = $change > 0 ? $change : 0;
		}
	}

	/**
	 * @param array $mineralChange Array of changes to save
	 */
	protected function storeMineralChanges(array $mineralChange): void {
		$this->_mineralNegativeChange = array();
		$this->_mineralPositiveChange = array();
		foreach ($mineralChange as $change) {
			$this->_mineralNegativeChange[] = $change < 0 ? $change : 0;
			$this->_mineralPositiveChange[] = $change > 0 ? $change : 0;
		}
	}
}
