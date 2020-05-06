<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\timeline;

use holonet\sc2calc\Utils;

/**
 * Events are logged on the timeline when a job is completed, or a checkpoint is
 * handled.
 */
class Event {
	/**
	 * @var string $description descriptive text for this event
	 */
	public $description;

	/**
	 * @var array $energySurplus surplus energy at the time this event is started (array of casters)
	 */
	public $energySurplus;

	/**
	 * @var int $gasSurplus surplus gas at the time this event is started
	 */
	public $gasSurplus;

	/**
	 * @var array $larvae surplus larvae at the time this event is started (array of hatcheries)
	 */
	public $larvae;

	/**
	 * @var int $mineralSurplus surplus mineral at the time this event is started
	 */
	public $mineralSurplus;

	/**
	 * @var int $order number that indicates in which order the events are created
	 */
	public $order;

	/**
	 * @var int $supplyCapacity supply capacity at the time of this event
	 */
	public $supplyCapacity;

	/**
	 * @var int $supplyCount used supply capacity at the time of this event
	 */
	public $supplyCount;

	/**
	 * @var float $timeCompleted time when this event is completed
	 */
	public $timeCompleted;

	/**
	 * @var float $timeStarted time when this event is started
	 */
	public $timeStarted;

	/**
	 * @return string representation of this event
	 */
	public function __toString(): string {
		return sprintf("\t|%5s|%5s|%5s|%5s|%5s|%5s|%5s|%5s|\n",
			$this->order, Utils::simple_time($this->timeStarted),
			"{$this->supplyCount} / {$this->supplyCapacity}",
			(count($this->larvae) ? (implode(', ', $this->larvae)) : ''),
			$this->description,
			round($this->mineralSurplus),
			round($this->gasSurplus),
			(isset($this->energySurplus) ? implode(', ', $this->energySurplus) : '')
		);
	}

	/**
	 * Export this event into a serialisable array.
	 */
	public function toArray(): array {
		return array(
			'order' => $this->order,
			'started' => Utils::simple_time($this->timeStarted),
			'completed' => Utils::simple_time($this->timeCompleted),
			'supply' => "{$this->supplyCount} / {$this->supplyCapacity}",
			'larvae' => $this->larvae,
			'object' => $this->description,
			'minerals' => round($this->mineralSurplus),
			'gas' => round($this->gasSurplus),
			'energy' => (isset($this->energySurplus) ? implode(', ', $this->energySurplus) : '')
		);
	}
}
