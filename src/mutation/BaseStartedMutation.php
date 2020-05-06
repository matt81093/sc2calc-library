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

/**
 * Mutation that adds a new base to income slots.
 */
class BaseStartedMutation extends Mutation {
	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'New base started';
	}

	/**
	 * Add new base to given income slot.
	 * {@inheritdoc}
	 */
	public function apply(IncomeSlot $slot): void {
		$slot->mineralMiners[] = 0;
		$slot->basesOperational[] = false;
	}
}
