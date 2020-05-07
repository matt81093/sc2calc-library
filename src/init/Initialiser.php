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
use holonet\sc2calc\job\BuildJob;
use holonet\sc2calc\logic\IncomeSlot;
use holonet\sc2calc\sets\IncomeSlots;
use holonet\sc2calc\timeline\Timeline;
use holonet\sc2calc\format\parser\Parser;
use holonet\sc2calc\sets\ProductsManager;
use holonet\sc2calc\logic\ProductionQueue;

/**
 * Class Initialiser should be extended to represent a game starting scenario.
 */
class Initialiser {
	//@todo maybe we need a version based data approach to this
	public const STARTING_WORKERS = 6;

	/**
	 * @var ProductsManager $productManager Collection holding all the parsed product objects
	 */
	protected $productManager;

	public function __construct(ProductsManager $productManager) {
		$this->productManager = $productManager;
	}

	/**
	 * Initialise the timeline based on the scenario represented by this class.
	 * @param Timeline $timeline The timeline to initialise
	 * @param Parser $parser Given parser instance with the unscheduled tasks
	 */
	public function initialiseTimeline(Timeline $timeline, Parser $parser): void {
		// initial income
		$timeline->income = new IncomeSlots(50, 0);
		if ($parser->options['startup build delay'] ?? 0 > 0) {
			$income = new IncomeSlot(0, $parser->options['startup build delay']);
			$income->mineralMiners = array();
			$timeline->income[] = $income;
			$income = new IncomeSlot($parser->options['startup build delay']);
			$income->mineralMiners = array(static::STARTING_WORKERS);
			$income->basesOperational = array(true);
			$timeline->income[] = $income;
		} else {
			$income = new IncomeSlot();
			$income->mineralMiners = array(6);
			$income->basesOperational = array(true);
			$timeline->income[] = $income;
		}

		$baseProduct = $this->productManager->designated($timeline->race, 'StartBase');

		//main building queue
		$timeline->queues->add(new ProductionQueue($baseProduct));

		//initialise supply counters and farms
		$timeline->supplyCount = 6;
		$timeline->farms->add(new Farm(0, $baseProduct->supplyCapacity));

		// create recurring worker job
		$job = new BuildJob($this->productManager->designated($timeline->race, 'Worker'));
		$job->recurring = true;
		$parser->jobs[] = $job;
	}
}
