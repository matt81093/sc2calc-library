<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;

/**
 * A hatchery is any structure that produces larvae.
 */
class Hatchery {
	public const LARVA_TIME = 15;

	/**
	 * @var float $created Time when hatchery was completed
	 */
	public $created;

	/**
	 * @var int $initialLarvae Initial number of larvae on this hatchery
	 */
	public $initialLarvae;

	/**
	 * @var int $larvae Number of larvae currently on this hatchery
	 */
	public $larvae = 0;

	/**
	 * @var float $nextLarvaGenerated Time when next larva will be generated
	 */
	public $nextLarvaGenerated;

	/**
	 * @var int $order Number that indicates in which order the hatcheries were created
	 */
	public $order;

	/**
	 * @var string|null $tag Tag to reference this specific hatchery
	 */
	public $tag;

	/**
	 * @var float $timeRebate rebate on time required to generate next larva
	 */
	public $timeRebate = 0;

	/**
	 * @var array $vomitExpires For each vomit, time when it expires
	 */
	public $vomitExpires = array();

	/**
	 * @var array $_generated For each larva, time when it was generated
	 */
	private $_generated = array();

	/**
	 * @var float $_lastUpdated Time when hatchery was last updated
	 */
	private $_lastUpdated;

	/**
	 * @var Product $spawnLarvae
	 */
	private $spawnLarvae;

	/**
	 * @param Product $spawnLarvae Reference to the ability "Spawnlarvae" product
	 * @param float $created Create time of the hatchery
	 * @param int $initialLarvae Number of initial larvae
	 * @param string|null $tag Specific reference to this Hatchery
	 */
	public function __construct(Product $spawnLarvae, float $created, int $initialLarvae = 1, string $tag = null) {
		$this->spawnLarvae = $spawnLarvae;
		$this->created = $created;
		$this->initialLarvae = $initialLarvae;
		$this->tag = $tag;
		$this->generateLarvae($this->created, $this->initialLarvae);
		$this->_lastUpdated = $this->created;
	}

	/**
	 * Generate given number of larvae at the given time.
	 * @param float $time The time to generate larvae at
	 * @param int $number Number of larvae to generate
	 * @param bool $resetGeneration Flag to reset the generation of larvae
	 */
	public function generateLarvae(float $time, int $number = 1, bool $resetGeneration = true): void {
		for ($i = 0; $i < $number; $i++) {
			$this->_generated[] = $time;
		}
		$this->larvae += $number;
		$this->larvae = min(19, $this->larvae);
		if ($resetGeneration) {
			$this->nextLarvaGenerated = $time + static::LARVA_TIME;
			$this->timeRebate = 0;
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Hatcheries[{$this->order}]::generateLarvae(), at ".Utils::simple_time($time).' we generate a larva. The next larva will be generated at '.Utils::simple_time($this->nextLarvaGenerated)." This hatchery now has {$this->larvae} larvae.\n";
			}
		} elseif ($this->larvae > 2) {
			$this->timeRebate = $time - $this->nextLarvaGenerated + static::LARVA_TIME;
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "Hatcheries[{$this->order}]::generateLarvae(), at ".Utils::simple_time($time).' we rise above 2 larvae. The next larva would be generated at '.Utils::simple_time($this->nextLarvaGenerated).", so the rebate is set at {$this->timeRebate} seconds.\n";
			}
		}
	}

	/**
	 * Export all larva timings into a string.
	 */
	public function getLarvaTimings(): string {
		$larvaTimings = array();
		foreach ($this->_generated as $generated) {
			$larvaTimings[] = Utils::simple_time($generated);
		}

		return implode(' > ', $larvaTimings);
	}

	/**
	 * @return float time when next larva is generated, INF if there are 3 or more larvae available
	 */
	public function nextGenerated(): float {
		return $this->larvae < 3 ? $this->nextLarvaGenerated : INF;
	}

	/**
	 * @return float get time when next vomit expires, INF if none
	 */
	public function nextVomit(): float {
		if (count($this->vomitExpires) > 0) {
			return min($this->vomitExpires);
		}

		return INF;
	}

	/**
	 * Calculate surplus number of larvae at a given time in the future.
	 */
	public function surplus(float $time): int {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Hatchery::surplus('.Utils::simple_time($time).")\n";
		}
		$hatchery = clone $this;
		$hatchery->update($time);

		return $hatchery->larvae;
	}

	/**
	 * Update the state of this hatchery up to given time.
	 */
	public function update(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Hatchery::update('.Utils::simple_time($time).")\n";
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Hatchery::update(), nextGenerated='.Utils::simple_time($this->nextGenerated()).', nextVomit='.Utils::simple_time($this->nextVomit())."\n";
		}
		while ($this->nextGenerated() <= $time || $this->nextVomit() <= $time) {
			// expire vomits
			foreach ($this->vomitExpires as $key => $vomitExpire) {
				if ($vomitExpire <= min($time, $this->nextGenerated())) {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo 'Hatchery::update(), expiring vomit at '.Utils::simple_time($vomitExpire)."\n";
					}
					unset($this->vomitExpires[$key]);
					$this->generateLarvae($vomitExpire, 4, false);
				}
			}

			// generate larvae
			if ($this->nextGenerated() <= $time) {
				if (Sc2Calc::$DEBUG_PRINT) {
					echo 'Hatchery::update(), generating larva at '.Utils::simple_time($this->nextLarvaGenerated)."\n";
				}
				$this->generateLarvae($this->nextLarvaGenerated);
			}
		}

		$this->_lastUpdated = max($this->created, $time);
	}

	/**
	 * Queue vomit on this hatchery at the given time.
	 */
	public function vomit(float $time): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'Hatchery::vomit('.Utils::simple_time($time).")\n";
		}
		$this->vomitExpires[] = $time + $this->spawnLarvae->timeCost;
		sort($this->vomitExpires);
	}

	/**
	 * Calculate time when the next larva is available.
	 */
	public function when(): float {
		if ($this->larvae > 0) {
			return $this->_lastUpdated;
		}

		return min($this->nextLarvaGenerated, $this->nextVomit());
	}

	/**
	 * Calculate time when another vomit can be queued on this hatchery.
	 */
	public function whenVomit(): float {
		return count($this->vomitExpires) > 0 ? max($this->vomitExpires) : $this->created;
	}
}
