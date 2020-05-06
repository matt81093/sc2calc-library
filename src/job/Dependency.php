<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

/**
 * Dependency of a job on another job.
 */
class Dependency {
	public const AT_COMPLETION = 0;

	public const AT_START = 1;

	/**
	 * @var Job $job Previous job on which it depends
	 */
	public $job;

	/**
	 * @var int $type whether the job can be scheduled after the start or completion
	 *          of the previous job
	 */
	public $type;

	public function __construct(Job $job, int $type) {
		$this->job = $job;
		$this->type = $type;
	}
}
