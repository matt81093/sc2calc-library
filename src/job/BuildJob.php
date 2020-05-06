<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

use holonet\sc2calc\enum\Race;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\sets\MutationSet;
use holonet\sc2calc\mutation\Mutation;

class BuildJob extends Job {
	/**
	 * @var Product $_product product to be built by this job
	 */
	private $_product;

	public function __construct(Product $product) {
		$this->_product = $product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function busiesQueues(): bool {
		return !in_array('Morph', $this->_product->types);
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return (string)$this->_product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function duration(): float {
		return $this->_product->timeCost;
	}

	/**
	 * {@inheritdoc}
	 */
	public function energyCost(): int {
		return $this->_product->energyCost;
	}

	/**
	 * {@inheritdoc}
	 */
	public function gasCost(): int {
		return $this->_product->gasCost;
	}

	/**
	 * {@inheritdoc}
	 */
	public function larvaCost(): int {
		return $this->_product->larvaCost;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mineralCost(): int {
		return $this->_product->mineralCost;
	}

	/**
	 * {@inheritdoc}
	 */
	public function morph(): bool {
		return in_array('Morph', $this->_product->types);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mutations(): MutationSet {
		$mutations = parent::mutations();

		// occupy worker
		if (in_array('Structure', $this->_product->types)) {

			// when does worker leave
			$workerLeaves = $this->timeInitiated;
			$travelTime = $this->timeStarted - $this->timeInitiated;

			// when does worker return
			switch ($this->_product->race) {
				case Race::PROTOSS():
					$workerReturns = $this->timeStarted + $travelTime;

					break;
				case Race::TERRAN():
					$workerReturns = $this->timeCompleted + $travelTime;

					break;
				case Race::ZERG():
				default:
					$workerReturns = INF;

					break;
			}

			// splice income
			if ($workerLeaves !== $workerReturns) {
				$mutations->add(new Mutation(-1, 0), $workerLeaves);
				if ($workerReturns !== INF) {
					$mutations->add(new Mutation(1, 0), $workerReturns);
				}
			}
		}

		$mutations->sort();

		return $mutations;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prerequisites(): array {
		return $this->_product->prerequisites;
	}

	/**
	 * {@inheritdoc}
	 */
	public function productBuilt(): ?Product {
		return $this->_product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function productsCreated(): array {
		if ($this->morph() && $this->_product->yields !== null) {
			return $this->_product->yields;
		}

		return array($this->_product);
	}

	/**
	 * {@inheritdoc}
	 */
	public function queueTypesCreated(): array {
		if ((in_array('Structure', $this->_product->types)) || (in_array('Spellcaster', $this->_product->types))) {
			return array($this->_product);
		}
		if ($this->morph() && $this->_product->yields !== null) {
			return $this->_product->yields;
		}

		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function queueTypesExpended(): ?array {
		if (!empty($this->_product->expends)) {
			$queueTypes = $this->_product->expends;
			$expendAll = $this->_product->expendsAll;
		} else {
			$queueTypes = array();
			$expendAll = false;
		}
		if (isset($this->queueTypeExpended)) {
			$queueTypes[] = $this->queueTypeExpended;
		}

		if (empty($queueTypes)) {
			return null;
		}

		return array($queueTypes, $expendAll);
	}

	/**
	 * @return Race of the product being built
	 */
	public function race(): Race {
		return $this->_product->race;
	}

	/**
	 * {@inheritdoc}
	 */
	public function spellcasterTypeExpended(): ?Product {
		return $this->_product->spellCaster;
	}

	/**
	 * {@inheritdoc}
	 */
	public function supplyCost($allowTrick = false): int {
		return $this->_product->supplyCost;
	}
}
