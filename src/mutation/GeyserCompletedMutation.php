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
use holonet\sc2calc\error\InvalidBuildException;

/**
 * Mutation that completes a new geyser.
 */
class GeyserCompletedMutation extends Mutation {
	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'New geyser completed';
	}

	/**
	 * Add new geyser to given income slot.
	 * {@inheritdoc}
	 */
	public function apply(IncomeSlot $slot): void {
		foreach ($slot->geysersOperational as $key => $geyserOperational) {
			if (!$geyserOperational) {
				$slot->geysersOperational[$key] = true;

				return;
			}
		}

		throw new InvalidBuildException('There is no geyser to be completed.');
	}
}
