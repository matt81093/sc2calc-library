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
 * Mutation that adds a new geyser to income slots.
 */
class GeyserStartedMutation extends Mutation {
	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'New geyser started';
	}

	/**
	 * Add new geyser to given income slot.
	 * {@inheritdoc}
	 */
	public function apply(IncomeSlot $slot): void {
		$slot->gasMiners[] = 0;
		$slot->geysersOperational[] = false;
	}
}
