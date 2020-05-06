<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;

/**
 * A spellcaster represents an entity that generates energy and can use that
 * energy to allow abilities to be executed.
 */
class Spellcaster {
	public const ENERGY_RATE = 0.5625;

	/**
	 * @var Product $casterType type of spellcaster
	 */
	public $casterType;

	/**
	 * @var float $created Time spellcaster was created
	 */
	public $created;

	/**
	 * @var float|null $destroyed Time spellcaster was destroyed
	 */
	public $destroyed;

	/**
	 * @var float Amount of energy stored at time of last update
	 */
	public $energy;

	/**
	 * @var EnergyReservation[] $reservations Reservations of future energy use
	 */
	public $reservations;

	/**
	 * @var string|null $tag Tag to reference this specific spellcaster
	 */
	public $tag;

	/**
	 * @var float $_lastUpdated Time of last update
	 */
	private $_lastUpdated;

	/**
	 * Create a new spellcaster.
	 * @param Product $casterType Type of caster
	 * @param float $created Time of creation
	 * @param string|null $tag Tag to reference this specific spellcaster
	 */
	public function __construct(Product $casterType, float $created = 0, string $tag = null) {
		$this->created = $created;
		$this->casterType = $casterType;
		$this->_lastUpdated = $created;
		$this->energy = $this->casterType->energyStart;
		$this->reservations = array();
		$this->tag = $tag;
	}

	/**
	 * Calculate energy available on this spellcaster at time it was last
	 * updated.
	 * @param bool $onlyFree if true, subtract energy that is reserved
	 * @return float Amount of energy
	 */
	public function energy(bool $onlyFree = true): float {
		$energy = $this->energy;
		if ($onlyFree) {
			foreach ($this->reservations as $reservation) {
				$energy -= $reservation->energy;
			}
		}

		return $energy;
	}

	/**
	 * Calculate surplus energy on this caster of the given type at a time in
	 * the future.
	 * @param float $time Time in the future
	 * @return float Amount of energy, ignoring reservations past given time
	 */
	public function surplus(float $time): float {
		$spellcaster = clone $this;
		$spellcaster->update($time);

		return $spellcaster->energy(false);
	}

	/**
	 * Update spellcaster up to given time.
	 */
	public function update(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Spellcaster::update(), updating '{$this->casterType->name}' to ".Utils::simple_time($time)."\n";
		}

		// remove expired reservations
		foreach ($this->reservations as $key => $reservation) {
			if ($reservation->time <= $this->_lastUpdated) {
				$this->energy -= $reservation->energy;
				unset($this->reservations[$key]);
			}
		}

		// update energy
		if ($time > $this->_lastUpdated) {
			$this->energy = min($this->casterType->energyMax,
				$this->energy + ($time - $this->_lastUpdated) * static::ENERGY_RATE);
			$this->_lastUpdated = $time;
		}
	}

	/**
	 * Calculate when the given amount of energy is available.
	 * @param float $energy Amount of energy
	 * @return float Time
	 */
	public function when(float $energy): float {
		$when = max(0, $energy - $this->energy()) / static::ENERGY_RATE + $this->_lastUpdated;
		if ($this->destroyed !== null && $when > $this->destroyed) {
			$when = INF;
		}

		return $when;
	}
}
