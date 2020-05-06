<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc;

use holonet\sc2calc\format\Parser;
use holonet\sc2calc\timeline\Timeline;
use holonet\sc2calc\timeline\Scheduler;
use holonet\sc2calc\sets\ProductsManager;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Center class used to work with the old reworked Timeline and Scheduler classes to optimise a build order.
 */
class Sc2Calc {
	/**
	 * @var bool $DEBUG_PRINT Debugging flag to enable debugging messages to stdout
	 */
	public static $DEBUG_PRINT = false;

	/**
	 * @var ProductsManager $productManager Collection holding all the parsed product objects
	 */
	private $productManager;

	public function __construct() {
		$this->productManager = ProductsManager::load();
	}

	/**
	 * @param string $buildOrder string to parse
	 * @return Sc2Build instance representing the parsed build
	 */
	public function fromBuildOrderString(string $buildOrder): Sc2Build {
		$stopwatch = new Stopwatch();

		$parser = new Parser($stopwatch, $this->productManager);
		$parser->parse($buildOrder);

		$timeline = $parser->createTimeline();

		// schedule jobs
		$scheduler = new Scheduler($stopwatch, $timeline, $parser->jobs);
		$scheduledJobs = $scheduler->schedule();

		$timeEnds = 0;
		foreach ($scheduledJobs as $job) {
			$timeEnds = max($timeEnds, $job->timeCompleted);
		}

		return new Sc2Build($stopwatch, $timeline, $timeEnds);
	}
}
