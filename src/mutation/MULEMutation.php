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
 * Mutation that adds a MULE to income slots.
 */
class MULEMutation extends Mutation {
	/**
	 * @var int $_MULEs number of MULEs to add
	 */
	private $_MULEs;

	public function __construct(int $MULEs) {
		$this->_MULEs = $MULEs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'Start MULE use';
	}

	/**
	 * Add a number of MULEs to given income slot.
	 * {@inheritdoc}
	 */
	public function apply(IncomeSlot $slot): void {
		$slot->MULEs += $this->_MULEs;
	}
}
