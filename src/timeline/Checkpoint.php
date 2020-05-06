<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\timeline;

/**
 * Checkpoints are fixed time events that are placed on the timeline to report
 * the state of the build at that time.
 */
class Checkpoint {
	/**
	 * @var string $description Descriptive text for this checkpoint
	 */
	public $description;

	/**
	 * @var float $timeCompleted Reported completion time of this checkpoint
	 */
	public $timeCompleted;

	/**
	 * @var float $timeStarted Time at which this checkpoint is triggered
	 */
	public $timeStarted;

	/**
	 * @param string $description Descriptive text for this checkpoint
	 * @param float $timeStarted Reported completion time of this checkpoint
	 * @param float|null $timeCompleted Time at which this checkpoint is triggered
	 */
	public function __construct(string $description, float $timeStarted, float $timeCompleted = null) {
		$this->description = $description;
		$this->timeStarted = $timeStarted;
		$this->timeCompleted = $timeCompleted ?? $timeStarted;
	}

	/**
	 * Compare two checkpoints by start time.
	 */
	public static function compare(self $checkpoint1, self $checkpoint2): int {
		return $checkpoint1->timeStarted <=> $checkpoint2->timeStarted;
	}
}
