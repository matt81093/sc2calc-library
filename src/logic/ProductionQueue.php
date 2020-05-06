<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

/**
 * A production queues represents an entity that has queue-like availability to
 * build certain objects. An obvious example would be a structure that produces
 * units or upgrades. The same mechanic is also used for building addons and
 * swapping addons.
 */
class ProductionQueue {
	/**
	 * @var float $available Time when production queue is next available
	 */
	public $available;

	/**
	 * @var float $busyTime Amount of time the production queue has been in use
	 */
	public $busyTime;

	/**
	 * @var float $chronoboosted Time when production queue was last chronoboosted
	 */
	public $chronoboosted;

	/**
	 * @var float $created Time when production queue was created
	 */
	public $created;

	/**
	 * @var float $destroyed Time when production queue was destroyed
	 */
	public $destroyed;

	/**
	 * @var Product $structure Type of production queue
	 */
	public $structure;

	/**
	 * @var string|null $tag Tag to reference this specific production queue
	 */
	public $tag;

	public function __construct(Product $structure, float $available = 0, string $tag = null) {
		$this->structure = $structure;
		$this->available = $available;
		$this->busyTime = 0;
		$this->created = $available;
		$this->tag = $tag;
	}

	public function __toString(): string {
		return (string)$this->structure;
	}

	/**
	 * Mark the production queue as busy for a given period of time.
	 * @param float $startTime
	 * @param float $endTime
	 * @param bool $busy
	 */
	public function busy($startTime, $endTime, $busy = true): void {
		$this->available = $endTime;
		if ($busy) {
			$this->busyTime += $endTime - $startTime;
		}
	}
}
