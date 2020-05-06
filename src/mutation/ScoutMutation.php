<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\mutation;

/**
 * Mutation that sends one worker to scout.
 */
class ScoutMutation extends Mutation {
	/**
	 * Create a new scout mutation.
	 */
	public function __construct() {
		$this->mineralChange = -1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return 'Send scout';
	}
}
