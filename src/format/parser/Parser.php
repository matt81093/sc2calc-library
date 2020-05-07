<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\format\parser;

use holonet\sc2calc\job\Job;
use holonet\sc2calc\enum\Race;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\init\Initialiser;
use holonet\sc2calc\timeline\Timeline;
use holonet\sc2calc\timeline\Checkpoint;
use holonet\sc2calc\sets\ProductsManager;
use Symfony\Component\Stopwatch\Stopwatch;
use holonet\sc2calc\error\InvalidBuildException;

/**
 * Parser base class to define an interface for a build string parser.
 */
abstract class Parser {
	/**
	 * @var float[] $checkpoints List of checkpoints read
	 */
	public $checkpoints;

	/**
	 * @var Job[] $jobs List of jobs read
	 */
	public $jobs;

	/**
	 * @var array $options List of options read
	 */
	public $options;

	/**
	 * @var Race $race The race of the parsed build order
	 */
	public $race;

	/**
	 * @var Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	public $stopwatch;

	/**
	 * @var ProductsManager $productManager Collection holding all the parsed product objects
	 */
	protected $productManager;

	/**
	 * @param Stopwatch $stopwatch Symfony stopwatch used to measure performance and speed
	 */
	public function __construct(Stopwatch $stopwatch, ProductsManager $productManager) {
		$this->stopwatch = $stopwatch;
		$this->productManager = $productManager;
	}

	/**
	 * Create an initialised timeline based on the parsed data.
	 */
	public function createTimeline(): Timeline {
		$timeline = new Timeline($this->stopwatch, $this->productManager, $this->race, $this->options);
		foreach ($this->checkpoints as $checkpoint) {
			$timeline->checkpoints[] = new Checkpoint('Checkpoint', $checkpoint);
		}

		//init timeline based on race
		$initialiser = $this->race->initialiser();
		/** @var Initialiser $initialiser */
		$initialiser = new $initialiser($this->productManager);
		$initialiser->initialiseTimeline($timeline, $this);

		return $timeline;
	}

	/**
	 * @param string $buildOrder string to parse
	 */
	public function parse(string $buildOrder): void {
		$this->stopwatch->start('Parser::parse');

		$this->checkpoints = array();
		$this->options = array();
		$this->jobs = array();

		$this->parseBody($buildOrder);

		if (count($this->jobs) === 0) {
			throw new InvalidBuildException('No commands found in build order!', 'Your build order is empty.');
		}

		// check racial consistency
		foreach ($this->jobs as $job) {
			if ($job->race() !== null) {
				if ($this->race !== null && !$job->race()->equals($this->race)) {
					throw new InvalidBuildException("The build order contains structures or units of more than one race (Had '{$this->race}' found '{$job->race()}'.");
				}
				$this->race = $job->race();
			}
		}

		if ($this->race === null) {
			throw new InvalidBuildException('The build order contains no units, structures, upgrades or morphs.');
		}

		sort($this->checkpoints);
		$this->stopwatch->stop('Parser::parse');
	}

	abstract protected function parseBody(string $build): void;
}
