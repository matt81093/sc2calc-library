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
 * Income slots are used to keep track of the resource income at different time
 * intervals of the build. Income slots are separated from adjacent slots by
 * mutations, such as the construction of a new worker or the transfer of
 * workers to a new base.
 */
class IncomeSlot {
	//@todo maybe we need a versioned data based approach to this
	public const MULE_MINING = 2.9;

	/**
	 * @var array $basesOperational for each base, whether the mining is already ongoing
	 */
	public $basesOperational = array();

	/**
	 * @var float $endTime Time when this slot ends
	 */
	public $endTime;

	/**
	 * @var array $gasMiners Number of gas miners per geyser
	 */
	public $gasMiners = array();

	/**
	 * @var array $geysersOperational for each geyser, whether the geyser is already operational
	 */
	public $geysersOperational = array();

	/**
	 * @var array $mineralMiners Number of mineral miners per base
	 */
	public $mineralMiners = array();

	/**
	 * @var int $MULEs Number of MULEs
	 */
	public $MULEs = 0;

	/**
	 * @var float $startTime Time when this slot starts
	 */
	public $startTime;

	/**
	 * @var float $_lastUpdated Time when slot was last updated
	 */
	private $_lastUpdated;

	public function __construct(float $startTime = 0, float $endTime = INF) {
		$this->start($startTime);
		$this->endTime = $endTime;
	}

	/**
	 * @return float calculated duration of this slot
	 */
	public function duration(): float {
		return $this->endTime - $this->_lastUpdated;
	}

	/**
	 * @return float calculated rate at which gas is mined in gas per second
	 */
	public function gasRate(): float {
		$gasRate = 0;
		for ($i = 0; $i < count($this->gasMiners); $i++) {
			if ($this->geysersOperational[$i]) {
				$gasRate += min($this->gasMiners[$i], 3) * 0.63;
			}
		}

		return $gasRate;
	}

	/**
	 * @return float calculated rate at which mineral is mined in mineral per second
	 */
	public function mineralRate(): float {
		$mineralRate = 0;
		for ($i = 0; $i < count($this->mineralMiners); $i++) {
			if ($this->basesOperational[$i]) {
				$mineralRate += min($this->mineralMiners[$i], 16) * 0.7
					+ min(max($this->mineralMiners[$i] - 16, 0), 8) * 0.3;
			}
		}

		return $mineralRate + $this->MULEs * static::MULE_MINING;
	}

	/**
	 * Reset start time of this slot.
	 */
	public function start(float $time): void {
		$this->startTime = $time;
		$this->_lastUpdated = $this->startTime;
	}

	/**
	 * Calculate surplus resources at a given time in the future.
	 * @return float[]
	 */
	public function surplus(float $time): array {
		if ($this->_lastUpdated <= $time && $this->endTime >= $time) {
			return array($this->gasRate() * ($time - $this->_lastUpdated),
				$this->mineralRate() * ($time - $this->_lastUpdated));
		}
		if ($this->_lastUpdated <= $time && $this->endTime <= $time) {
			return array($this->gasRate() * ($this->endTime - $this->_lastUpdated),
				$this->mineralRate() * ($this->endTime - $this->_lastUpdated));
		}

		return array(0, 0);
	}

	/**
	 * Update this slot up to the given time.
	 */
	public function update(float $time): void {
		$this->_lastUpdated = min($this->endTime, max($this->startTime, $time));
	}

	/**
	 * Calculate when the needed amount of resources would be available.
	 * @param float $mineralNeeded
	 * @param float $gasNeeded
	 * @return float[]
	 */
	public function when($mineralNeeded, $gasNeeded): array {
		if ($this->duration() === 0.0) {
			return array(INF, INF);
		}

		// when is mineral achieved
		if ($mineralNeeded <= 0) {
			$mineralTime = $this->_lastUpdated;
		} elseif ($this->mineralRate() === 0.0) {
			$mineralTime = INF;
		} else {
			$mineralTime = $mineralNeeded / $this->mineralRate() + $this->_lastUpdated;
			if ($mineralTime > $this->endTime) {
				$mineralTime = INF;
			}
		}

		// when is gas achieved
		if ($gasNeeded <= 0) {
			$gasTime = $this->_lastUpdated;
		} elseif ($this->gasRate() === 0.0) {
			$gasTime = INF;
		} else {
			$gasTime = $gasNeeded / $this->gasRate() + $this->_lastUpdated;
			if ($gasTime > $this->endTime) {
				$gasTime = INF;
			}
		}

		return array($mineralTime, $gasTime);
	}
}
