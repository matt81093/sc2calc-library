<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\init;

use holonet\sc2calc\format\Parser;
use holonet\sc2calc\logic\Spellcaster;
use holonet\sc2calc\timeline\Timeline;

/**
 * ProtossInitialiser to be used for a normal Protoss 1v1 ladder game start.
 */
class ProtossInitialiser extends Initialiser {
	public const WARPGATE_QUEUE_REDUCTION = 10;

	/**
	 * {@inheritdoc}
	 */
	public function initialiseTimeline(Timeline $timeline, Parser $parser): void {
		parent::initialiseTimeline($timeline, $parser);
		$product = $this->productManager->byIdentifier('Nexus');
		$timeline->spellcasters->add(new Spellcaster($product, 0));
	}
}
