<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

use holonet\sc2calc\logic\Product;
use holonet\sc2calc\mutation\ScoutMutation;

class ScoutJob extends MutateJob {
	/**
	 * @var Product $scoutingWorker Reference to the product of the scouting worker unit
	 */
	private $scoutingWorker;

	public function __construct(Product $scoutingWorker, float $delay = 0) {
		$mutation = new ScoutMutation();
		$mutation->delay = $delay;
		$this->scoutingWorker = $scoutingWorker;
		parent::__construct($mutation);
	}

	/**
	 * {@inheritdoc}
	 */
	public function queueTypesCreated(): array {
		return array($this->scoutingWorker);
	}
}
