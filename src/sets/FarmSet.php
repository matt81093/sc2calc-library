<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use holonet\sc2calc\logic\Farm;

/**
 * Farms are the total collection of farms that are available.
 */
class FarmSet {
	/**
	 * @var Farm[] $_farms List of farms, always kept sorted by time created
	 */
	private $_farms = array();

	/**
	 * @param Farm $farm farm to be added to the list
	 */
	public function add(Farm $farm): void {
		$this->_farms[] = $farm;
		uasort($this->_farms, array(Farm::class, 'compare'));
	}

	/**
	 * Remove a farm with the given supply capacity at the given time.
	 * @param int $supplyCapacity Supply capacity to remove
	 * @param float $time Time of removal
	 */
	public function remove(int $supplyCapacity, float $time): void {
		foreach ($this->_farms as &$farm) {
			if ($farm->capacity === $supplyCapacity && $farm->created <= $time) {
				$farm->destroyed = $time;

				break;
			}
		}
	}

	/**
	 * Calculate supply capacity at a given time.
	 */
	public function surplus(float $time): int {
		$capacity = 0;
		foreach ($this->_farms as $farm) {
			if ($farm->created <= $time && $farm->destroyed >= $time) {
				$capacity += $farm->capacity;
			}
		}

		return $capacity;
	}

	/**
	 * Calculate when the supply capacity is greater than or equal to the given
	 * capacity.
	 * @return float|null The time the capacity is reached or null if never
	 */
	public function when(int $capacity): ?float {
		foreach ($this->_farms as $farm) {
			if ($farm->destroyed === null) {
				$capacity -= $farm->capacity;
				if ($capacity <= 0) {
					return $farm->created;
				}
			}
		}

		return null;
	}
}
