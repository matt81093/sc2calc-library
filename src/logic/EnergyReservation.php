<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

/**
 * Energy reservations are used to reserve energy on a spellcaster for future
 * use. The need for this stems from the fact that the scheduler cannot always
 * process energy consumption in chronological order.
 */
class EnergyReservation {
	/**
	 * @var int $energy Amount of energy to be reserved
	 */
	public $energy;

	/**
	 * @var float $time Time at which energy is reserved
	 */
	public $time;

	public function __construct(float $time, int $energy) {
		$this->energy = $energy;
		$this->time = $time;
	}
}
