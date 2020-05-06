<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use LogicException;
use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\logic\Spellcaster;
use holonet\sc2calc\logic\EnergyReservation;

/**
 * SpellcasterSet is a set of spellcaster objects, with functions to choose one of
 * those spellcasters.
 */
class SpellcasterSet {
	/**
	 * @var Spellcaster[] $_spellcasters List of spellcasters
	 */
	private $_spellcasters = array();

	/**
	 * Clone list of spellcasters.
	 */
	public function __clone() {
		$spellcasters = array();
		foreach ($this->_spellcasters as $spellcaster) {
			$spellcasters[] = clone $spellcaster;
		}
		$this->_spellcasters = $spellcasters;
	}

	/**
	 * @param Spellcaster $spellcaster Spellcaster to be added
	 */
	public function add(Spellcaster $spellcaster): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Spellcasters::add({$spellcaster->casterType} at ".Utils::simple_time($spellcaster->created).")\n";
		}
		$this->_spellcasters[] = $spellcaster;
	}

	/**
	 * Get the spellcaster of the given type that has the most free energy.
	 * @param Product|null $casterType The caster type to count the energy for, or null if any
	 * @param string[] $tagsRequired
	 */
	public function choose(?Product $casterType, float $time, array $tagsRequired = null): ?Spellcaster {
		if (Sc2Calc::$DEBUG_PRINT) {
			printf(
				'Spellcasters::choose(%s, %s, %s%s',
				$casterType->name ?? 'null', Utils::simple_time($time),
				$tagsRequired === null ? 'null' : implode(', ', $tagsRequired), "\n"
			);
		}
		foreach ($this->select($casterType, $tagsRequired) as $spellcaster) {
			if ($spellcaster->created <= $time && $spellcaster->destroyed === null || $spellcaster->destroyed >= $time) {
				if (!isset($candidate)) {
					$candidate = $spellcaster;
				} elseif ($spellcaster->energy() > $candidate->energy()) {
					$candidate = $spellcaster;
				}
			}
		}
		if (isset($candidate)) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Spellcasters::choose(), chosen {$candidate->casterType} created at ".Utils::simple_time($candidate->created)."\n";
			}

			return $candidate;
		}

		return null;
	}

	/**
	 * Expend the given amount of energy from a spellcaster of the given type.
	 * Uses whichever spellcaster has the most free energy.
	 * @param Product $casterType Type of spellcaster
	 * @param int $energy Energy to be expended
	 * @param string[] $tagsRequired
	 * @return Spellcaster Spellcaster used
	 */
	public function expend(?Product $casterType, int $energy, float $time, array $tagsRequired = null): Spellcaster {
		if (Sc2Calc::$DEBUG_PRINT) {
			printf(
				'Spellcasters::expend(%s, %d, %s, %s)%s',
				$casterType->name ?? 'null', $energy, Utils::simple_time($time),
				$tagsRequired === null ? 'null' : implode(', ', $tagsRequired), "\n"
			);
		}
		$spellcaster = $this->choose($casterType, $time, $tagsRequired);

		if ($spellcaster === null) {
			throw new LogicException(sprintf("No spellcaster of type '%s' is available.", $casterType->name ?? 'null'));
		}

		if (round($spellcaster->energy()) < $energy) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Spellcasters::expend(), chosen spellcaster has {$spellcaster->energy()} free energy at ".Utils::simple_time($time).".\n";
			}

			throw new LogicException(sprintf("No spellcaster of type '%s' has enough energy.", $casterType->name ?? 'null'));
		}
		$spellcaster->energy -= $energy;

		return $spellcaster;
	}

	/**
	 * Remove the spellcaster of the given type with the least energy.
	 * @param float $time Time of removal
	 */
	public function remove(Product $casterType, float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Spellcasters::remove({$casterType}, ".Utils::simple_time($time).")\n";
		}

		// choose a spellcaster to remove
		//@todo used to have $tagsRequired parameter in search but not function
		foreach ($this->select($casterType) as $spellcaster) {
			if ($spellcaster->created <= $time) {
				if (!isset($candidate)) {
					$candidate = $spellcaster;
				} elseif ($spellcaster->energy() < $candidate->energy()) {
					$candidate = $spellcaster;
				}
			}
		}

		// if no such spellcaster exists, throw an error
		if (!isset($candidate)) {
			throw new LogicException("No spellcaster of type '{$casterType->name}' could be removed.");
		}

		// mark it as destroyed
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "Spellcasters::remove(), chosen {$candidate->casterType} created at ".Utils::simple_time($candidate->created)."\n";
		}
		$candidate->destroyed = $time;
	}

	/**
	 * Reserves given amount of energy on an spellcaster of the given type.
	 * @param Product $casterType Type of spellcaster
	 * @param int $energy Energy to be reserved
	 * @param float $time Time of reservation
	 * @param string[] $tagsRequired
	 * @return Spellcaster Spellcaster used
	 */
	public function reserve(Product $casterType, int $energy, float $time, array $tagsRequired = null): Spellcaster {
		$spellcaster = $this->choose($casterType, $time, $tagsRequired);
		if (!isset($spellcaster)) {
			throw new LogicException("No spellcaster of type '{$casterType->name}' exists.");
		}
		$spellcaster->reservations[] = new EnergyReservation($time, $energy);

		return $spellcaster;
	}

	/**
	 * Find all spellcasters of given type with one of the given tags.
	 * @param Product|null $casterType The caster type to count the energy for, or null if any
	 * @param string[] $tagsRequired
	 * @return Spellcaster[] Array of references to the spellcasters
	 */
	public function select(?Product $casterType = null, array $tagsRequired = null): array {
		$spellcasters = array();
		foreach ($this->_spellcasters as $spellcaster) {
			if ($casterType === null || $spellcaster->casterType->uid === $casterType->uid) {
				if ($tagsRequired === null || (isset($spellcaster->tag) && in_array($spellcaster->tag, $tagsRequired))) {
					$spellcasters[] = $spellcaster;
				}
			}
		}

		if (Sc2Calc::$DEBUG_PRINT) {
			printf(
				'SpellcasterSet::select(%s, [%s]), Found %d spellcasters%s',
				$casterType->name ?? 'null',
				implode(', ', $tagsRequired ?? array()),
				count($spellcasters), "\n"
			);
		}

		return $spellcasters;
	}

	/**
	 * Calculate surplus energy on all casters of the given type at a time in
	 * the future.
	 * @param Product|null $casterType Type of caster; if null, all casters are returned
	 * @param string[] $tagsRequired
	 * @return int[] array with energy surpluses
	 */
	public function surplus(?Product $casterType, float $time, array $tagsRequired = null): array {
		$surplus = array();
		foreach ($this->select($casterType, $tagsRequired) as $spellcaster) {
			if ($spellcaster->created <= $time && $spellcaster->destroyed >= $time) {
				$surplus[] = (int)round($spellcaster->surplus($time));
			}
		}

		return $surplus;
	}

	/**
	 * Update spellcasters up to given time.
	 */
	public function update(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'SpellcasterSet::update('.Utils::simple_time($time).")\n";
		}
		foreach ($this->_spellcasters as $spellcaster) {
			$spellcaster->update($time);
		}
	}

	/**
	 * Calculate time when a caster of the given type has the given amount of
	 * free energy.
	 * @param string[] $tagsRequired
	 * @param ?Product $casterType
	 * @return float|null time or null if never
	 */
	public function when(?Product $casterType, int $energy, array $tagsRequired = null): ?float {
		if (Sc2Calc::$DEBUG_PRINT) {
			printf('SpellcasterSet::when(%s, %d)%s', $casterType->name ?? 'null', $energy, "\n");
		}
		$time = INF;
		foreach ($this->select($casterType, $tagsRequired) as $spellCaster) {
			$time = min($time, $spellCaster->when($energy));
		}

		return $time;
	}
}
