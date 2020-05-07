<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\init;

use holonet\sc2calc\logic\Farm;
use holonet\sc2calc\logic\Hatchery;
use holonet\sc2calc\timeline\Timeline;
use holonet\sc2calc\format\parser\Parser;

/**
 * ProtossInitialiser to be used for a normal Zerg 1v1 ladder game start.
 */
class ZergInitialiser extends Initialiser {
	/**
	 * {@inheritdoc}
	 */
	public function initialiseTimeline(Timeline $timeline, Parser $parser): void {
		parent::initialiseTimeline($timeline, $parser);
		$timeline->hatcheries->add(new Hatchery($this->productManager->byIdentifier('SpawnLarvae'), 0, 3));
		$timeline->farms->add(new Farm(0, $this->productManager->byIdentifier('Overlord')->supplyCapacity));
	}
}
