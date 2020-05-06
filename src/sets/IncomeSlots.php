<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use Countable;
use ArrayAccess;
use LogicException;
use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\logic\IncomeSlot;
use holonet\sc2calc\mutation\Mutation;

/**
 * Set of all income slots currently known to exist.
 * @see IncomeSlot
 */
class IncomeSlots implements ArrayAccess, Countable {
	/**
	 * @var float $_gasStored Amount of gas stored
	 */
	public $_gasStored;

	/**
	 * @var float $_mineralStored Amount of mineral stored
	 */
	public $_mineralStored;

	/**
	 * @var float $_initialGasStored Amount of gas stored at beginning of game
	 */
	private $_initialGasStored;

	/**
	 * @var float $_initialMineralStored Amount of mineral stored at beginning of game
	 */
	private $_initialMineralStored;

	/**
	 * @var float $_lastUpdated Time when income slots were last updated
	 */
	private $_lastUpdated;

	/**
	 * @var IncomeSlot[] $_slots List of income slots
	 */
	private $_slots = array();

	/**
	 * Create new list of income slots.
	 */
	public function __construct(float $initialMineral = 0, float $initialGas = 0) {
		$this->_initialGasStored = $initialGas;
		$this->_initialMineralStored = $initialMineral;
		$this->_lastUpdated = 0;
		$this->_gasStored = $this->_initialGasStored;
		$this->_mineralStored = $this->_initialMineralStored;
	}

	/**
	 * Clone this list of income slots.
	 */
	public function __clone() {
		$slots = array();
		foreach ($this->_slots as $slot) {
			$slots[] = clone $slot;
		}
		$this->_slots = $slots;
	}

	/// Countable implementation
	public function count() {
		return count($this->_slots);
	}

	/**
	 * Expend the given amount of resources from the income slots, eating up the
	 * earliest slots first.
	 * @param float $mineral
	 * @param float $gas
	 */
	public function expend($mineral, $gas): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "IncomeSlots::expend({$mineral}, {$gas})\n";
		}
		$this->_gasStored = round($this->_gasStored - $gas);
		$this->_mineralStored = round($this->_mineralStored - $mineral);
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "IncomeSlots::expend(), after expending, we got {$this->_mineralStored} minerals and {$this->_gasStored} gas.\n";
		}
	}

	/// ArrayAccess implementation
	public function offsetExists($key) {
		return isset($this->_slots[$key]);
	}

	public function offsetGet($key) {
		return $this->_slots[$key];
	}

	public function offsetSet($key, $value): void {
		if (null === $key) {
			$this->_slots[] = $value;
		} else {
			$this->_slots[$key] = $value;
		}
	}

	public function offsetUnset($key): void {
		unset($this->_slots[$key]);
	}

	/**
	 * Splice a mutation into these income slots. The mutation will split
	 * one slot in twain, and affect all slots after its mutation point.
	 */
	public function splice(Mutation $mutation): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'IncomeSlots::splice('.$mutation.' @ '.Utils::simple_time($mutation->time).")\n";
		}

		// find splice point
		for ($i = 0; $i < count($this->_slots); $i++) {
			$slot = $this->_slots[$i];
			if ($slot->endTime === null || $slot->endTime > $mutation->time) {
				$newSlot = clone $slot;
				$newSlot->start($mutation->time);
				$slot->endTime = $mutation->time;
				array_splice($this->_slots, $i + 1, 0, array($newSlot));
				$spliceSlot = $i + 1;

				break;
			}
		}

		if (!isset($spliceSlot)) {
			throw new LogicException('Could not find splice slot');
		}

		if (isset($mutation->delay)) {
			// update income beyond negative splice point
			for ($i = $spliceSlot; $i < count($this->_slots); $i++) {
				$mutation->applyNegative($this->_slots[$i]);
			}

			// find positive splice point
			for ($i = 0; $i < count($this->_slots); $i++) {
				$slot = $this->_slots[$i];
				if ($slot->endTime === null || $slot->endTime > $mutation->time + $mutation->delay) {
					$newSlot = clone $slot;
					$newSlot->start($mutation->time + $mutation->delay);
					$slot->endTime = $mutation->time + $mutation->delay;
					array_splice($this->_slots, $i + 1, 0, array($newSlot));
					$spliceSlot = $i + 1;

					break;
				}
			}

			// update income beyond positive splice point
			for ($i = $spliceSlot; $i < count($this->_slots); $i++) {
				$mutation->applyPositive($this->_slots[$i]);
			}
		} else {
			// update income beyond splice point
			for ($i = $spliceSlot; $i < count($this->_slots); $i++) {
				$mutation->apply($this->_slots[$i]);
			}
		}
	}

	/**
	 * Calculate surplus gas and mineral at a given time in the future.
	 * @return float[]
	 */
	public function surplus(float $time): array {
		$gasSurplus = $this->_gasStored;
		$mineralSurplus = $this->_mineralStored;
		foreach ($this->_slots as $slot) {
			list($gas, $mineral) = $slot->surplus($time);
			$mineralSurplus += $mineral;
			$gasSurplus += $gas;
		}

		return array($gasSurplus, $mineralSurplus);
	}

	/**
	 * Calculate total gas mined before given time.
	 */
	public function totalGas(float $time): float {
		$totalGas = $this->_initialGasStored;
		foreach ($this->_slots as $slot) {
			$overlap = min($time, $slot->endTime ?? INF) - min($time, $slot->startTime);
			$totalGas += $overlap * $slot->gasRate();
		}

		return $totalGas;
	}

	/**
	 * Calculate total minerals mined before given time.
	 */
	public function totalMineral(float $time): float {
		$totalMineral = $this->_initialMineralStored;
		foreach ($this->_slots as $slot) {
			$overlap = min($time, $slot->endTime ?? INF) - min($time, $slot->startTime);
			$totalMineral += $overlap * $slot->mineralRate();
		}

		return $totalMineral;
	}

	/**
	 * Update time slots up to the given time.
	 */
	public function update(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'IncomeSlots::update('.Utils::simple_time($time).")\n";
		}
		foreach ($this->_slots as $slot) {
			list($gasSurplus, $mineralSurplus) = $slot->surplus($time);
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "IncomeSlots::update(), we gain {$mineralSurplus} minerals and {$gasSurplus} gas";
				echo " from slot starting at {$slot->startTime} and ending at {$slot->endTime}\n";
			}
			$this->_gasStored += $gasSurplus;
			$this->_mineralStored += $mineralSurplus;
			$slot->update($time);
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "IncomeSlots::update(), we got a total of {$this->_mineralStored} minerals and {$this->_gasStored} gas.\n";
		}
		$this->_lastUpdated = $time;
	}

	/**
	 * Calculate when the given amount of resources is available.
	 */
	public function when(float $mineralNeeded, float $gasNeeded): float {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'IncomeSlots::when('.$mineralNeeded.', '.$gasNeeded.")\n";
		}

		// how much is needed
		$mineralNeeded -= $this->_mineralStored;
		$gasNeeded -= $this->_gasStored;

		// calculate breaking points
		foreach ($this->_slots as $slot) {
			list($mineralTimeInSlot, $gasTimeInSlot) = $slot->when($mineralNeeded, $gasNeeded);

			if ($mineralTimeInSlot === INF) {
				$mineralNeeded -= $slot->mineralRate() * $slot->duration();
			} elseif (!isset($mineralTime)) {
				$mineralTime = $mineralTimeInSlot;
			}

			if ($gasTimeInSlot === INF) {
				$gasNeeded -= $slot->gasRate() * $slot->duration();
			} elseif (!isset($gasTime)) {
				$gasTime = $gasTimeInSlot;
			}

			if (isset($gasTime, $mineralTime)) {
				break;
			}
		}

		if (!isset($gasTime)) {
			$gasTime = INF;
		}
		if (!isset($mineralTime)) {
			$mineralTime = INF;
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			echo "IncomeSlots::when(), mineralTime={$mineralTime}, gasTime={$gasTime}\n";
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "IncomeSlots::when(), storedMineral={$this->_mineralStored}, storedGas={$this->_gasStored}\n";
		}

		return max($mineralTime, $gasTime);
	}
}
