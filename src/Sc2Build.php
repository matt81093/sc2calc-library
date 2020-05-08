<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc;

use holonet\sc2calc\enum\Race;
use holonet\sc2calc\timeline\Timeline;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Logical class representing a build order (our "model" if you will).
 */
class Sc2Build {
	/**
	 * @var float $endTime The time of the last scheduled job in the build
	 */
	public $endTime;

	/**
	 * @var Race $race race of the build order
	 */
	public $race;

	/**
	 * @var Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	public $stopwatch;

	/**
	 * @var Timeline $timeline The timeline of the build
	 */
	public $timeline;

	/**
	 * @param Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 * @param Timeline $timeline The timeline of the build, as parsed and scheduled
	 * @param float $endTime The time of the last scheduled job in the build
	 */
	public function __construct(Stopwatch $stopwatch, Timeline $timeline, float $endTime) {
		$this->stopwatch = $stopwatch;
		$this->timeline = $timeline;
		$this->endTime = $endTime;
		$this->race = $timeline->race;
		$timeline->queues->timeEnds = $endTime;
	}
}
