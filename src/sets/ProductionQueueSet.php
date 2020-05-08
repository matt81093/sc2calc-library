<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use jc21\CliTable;
use LogicException;
use holonet\sc2calc\Utils;
use holonet\sc2calc\Sc2Calc;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\logic\ProductionQueue;

/**
 * A set of production queues with functions to choose which of those queues to
 * expend.
 */
class ProductionQueueSet {
	/**
	 * @var float $timeEnds the end of the timeline
	 */
	public $timeEnds;

	/**
	 * @var float $_lastUpdated Time when queues were last updated
	 */
	private $_lastUpdated = 0;

	/**
	 * @var ProductionQueue[] $_queues List of production queues
	 */
	private $_queues = array();

	public function __clone() {
		$queues = array();
		foreach ($this->_queues as $queue) {
			$queues[] = clone $queue;
		}
		$this->_queues = $queues;
	}

	public function __toString(): string {
		$cliTable = new CliTable();
		$cliTable->addField('Structure', 'structure');
		$cliTable->addField('Created', 'created');
		$cliTable->addField('Destroyed', 'destroyed');
		$cliTable->addField('Busy time', 'busy_time');
		$cliTable->addField('Busy percentage', 'busy_percentage');
		$cliTable->injectData($this->toArray());

		return $cliTable->get();
	}

	/**
	 * @param ProductionQueue $queue Queue object to be added to the list
	 */
	public function add(ProductionQueue $queue): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "ProductionQueues::add({$queue->structure})\n";
		}
		$this->_queues[] = $queue;
	}

	/**
	 * Find next available queues, either of any expended structure, or
	 * of all expended structures.
	 * @param Product[] $expends Expended structures
	 * @param bool $expendsAll If true, find all expended structures
	 * @param string[] $tagsRequired
	 * @return ProductionQueue[] Array of references to the available queues
	 */
	public function choose(float $time, array $expends, bool $expendsAll = false, array $tagsRequired = null): array {
		$queues = array();

		foreach ($expends as $expend) {
			if ($expendsAll) {
				unset($candidate);
			}
			foreach ($this->select($expend, $tagsRequired) as $queue) {
				if ($queue->available <= $time) {
					if (!isset($candidate)) {
						$candidate = $queue;
					} elseif ($candidate->chronoboosted === null) {
						$candidate = $queue;
					} elseif ($candidate->chronoboosted < $queue->chronoboosted) {
						$candidate = $queue;
					}
				} else {
					if (Sc2Calc::$DEBUG_PRINT) {
						echo 'ProductionQueues::choose(), queue rejected: available at '.Utils::simple_time($queue->available).', but needed at '.Utils::simple_time($time)."\n";
					}
				}
			}
			if ($expendsAll) {
				if (!isset($candidate)) {
					throw new LogicException("No production queue of type '{$expend}' is available!");
				}
				$queues[] = $candidate;
			}
		}
		if (!isset($candidate)) {
			throw new LogicException("No production queue of type '".implode("', or '", $expends)."' is available!");
		}

		if (!$expendsAll) {
			return array($candidate);
		}

		return $queues;
	}

	/**
	 * Mark existing queues as destroyed, and create new queues of different
	 * types. The newly created queues inherit tags from the destroyed queues
	 * in the order in which they appear.
	 * @param array $queuesDestroyed
	 * @param float $timeDestroyed
	 * @param array $queueTypesCreated
	 * @param float $timeCreated
	 */
	public function morph($queuesDestroyed, $timeDestroyed, $queueTypesCreated, $timeCreated): void {
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'ProductionQueues::morph('.implode(' + ', $queuesDestroyed).', '.Utils::simple_time($timeDestroyed).', '.
				implode(' + ', $queueTypesCreated).', '.Utils::simple_time($timeCreated).")\n";
		}

		// queues destroyed
		foreach ($queuesDestroyed as $queue) {
			$queue->destroyed = $timeDestroyed;
		}

		$tag = null;

		// queues created
		for ($i = 0; $i < count($queueTypesCreated); $i++) {
			if ($queueTypesCreated[$i] !== null) {
				if (isset($queuesDestroyed[$i])) {
					$tag = $queuesDestroyed[$i]->tag;
				}
				if (Sc2Calc::$DEBUG_PRINT) {
					echo "ProductionQueues::morph(), queue {$queueTypesCreated[$i]} gets tag ".($tag ?? 'null')."\n";
				}
				$this->_queues[] = new ProductionQueue($queueTypesCreated[$i], $timeCreated, $tag);
			}
		}
	}

	/**
	 * Find all queues of given structure with one of the given tags.
	 * @param Product $structure Structure type of the queues
	 * @param string[] $tagsRequired
	 * @return ProductionQueue[] Array of references to the queues
	 */
	public function select(Product $structure, array $tagsRequired = null): array {
		$queues = array();
		foreach ($this->_queues as $queue) {
			if ($queue->structure->uid === $structure->uid && !isset($queue->destroyed)) {
				if ($tagsRequired === null || (isset($queue->tag) && in_array($queue->tag, $tagsRequired))) {
					$queues[] = $queue;
				}
			}
		}

		return $queues;
	}

	/**
	 * Export the queues and their usage times into a serialisable array.
	 */
	public function toArray(): array {
		$ret = array();
		foreach ($this->_queues as $queue) {
			$existed = (isset($queue->destroyed) ? $queue->destroyed : $this->timeEnds) - $queue->created;
			if ($queue->busyTime !== 0 && $existed !== 0) {
				$ret[] = array(
					'structure' => $queue->structure->name,
					'created' => Utils::simple_time($queue->created),
					'destroyed' => (isset($queue->destroyed) ? Utils::simple_time($queue->destroyed) : ''),
					'busy_time' => Utils::simple_time($queue->busyTime),
					'busy_percentage' => number_format(100 * $queue->busyTime / $existed).'%'
				);
			}
		}

		return $ret;
	}

	/**
	 * Update all production queues up to given time.
	 */
	public function update(float $time): void {
		$this->_lastUpdated = $time;
	}

	/**
	 * Calculate when the given queue types are available.
	 * @param string[] $tagsRequired
	 * @psalm-return array{float, Product[]|null}
	 */
	public function when(array $queueTypes, bool $expendsAll, array $tagsRequired = null): array {
		if (!isset($queueTypes) || count($queueTypes) === 0) {
			return array($this->_lastUpdated, null);
		}

		// when are production queues available
		if (Sc2Calc::$DEBUG_PRINT) {
			echo "ProductionQueues::when(), Looking for available queues\n";
		}
		$queuesAvailable = $expendsAll ? 0 : INF;
		$unavailableQueues = array();

		foreach ($queueTypes as $expend) {
			$queues = $this->select($expend, $tagsRequired);

			// when is production queue of this type available
			$queueAvailable = INF;
			foreach ($queues as $queue) {
				$queueAvailable = min($queueAvailable, $queue->available);
			}
			if (Sc2Calc::$DEBUG_PRINT) {
				echo 'ProductionQueues::when(), '.count($queues)." Queues of type {$expend}, earliest available at ".Utils::simple_time($queueAvailable)."\n";
			}
			if ($queueAvailable === INF) {
				$unavailableQueues[] = $expend;
			}
			if ($expendsAll) {
				$queuesAvailable = max($queuesAvailable, $queueAvailable);
			} else {
				$queuesAvailable = min($queuesAvailable, $queueAvailable);
			}
		}
		if (Sc2Calc::$DEBUG_PRINT) {
			echo 'ProductionQueues::when(), all queues available at '.Utils::simple_time($queuesAvailable)."\n";
		}

		// some or all queues are unavailable
		if ($queuesAvailable === INF) {
			if (Sc2Calc::$DEBUG_PRINT) {
				echo "ProductionQueues::when(), no production queue of type '".implode("', '", $unavailableQueues)."' is available.\n";
			}

			return array($queuesAvailable, $unavailableQueues);
		}

		return array(Utils::floatmax($this->_lastUpdated, $queuesAvailable), null);
	}
}
