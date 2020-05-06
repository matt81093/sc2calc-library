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
 * Mutation that completes a new base.
 */
class BaseCompletedMutation extends Mutation {
	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'New base completed';
	}

	/**
	 * Add new base to given income slot.
	 * {@inheritdoc}
	 */
	public function apply(IncomeSlot $slot): void {
		foreach ($slot->basesOperational as $key => $baseOperational) {
			if (!$baseOperational) {
				$slot->basesOperational[$key] = true;

				return;
			}
		}

		throw new InvalidBuildException('There is no base to be completed.');
	}
}
