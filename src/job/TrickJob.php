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
use holonet\sc2calc\error\InvalidBuildException;

class TrickJob extends Job {
	/**
	 * @var int $_pledgeCount number of pledge products to build
	 */
	private $_pledgeCount;

	/**
	 * @var Product $_pledgeProduct type of structure to build, usually Extractor
	 */
	private $_pledgeProduct;

	/**
	 * @var int $_turnCount number of turn products to build
	 */
	private $_turnCount;

	/**
	 * @var Product|null $_turnProduct type of unit to build, usually Drone
	 */
	private $_turnProduct;

	/**
	 * @param Product $turnProduct
	 */
	public function __construct(Product $pledgeProduct, int $pledgeCount, Product $turnProduct = null, int $turnCount = 0) {
		$this->_pledgeProduct = $pledgeProduct;
		$this->_pledgeCount = $pledgeCount;
		$this->_turnProduct = $turnProduct;
		$this->_turnCount = $turnCount;
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		switch ($this->_pledgeCount) {
			case 1:
				$result = '';

				break;
			case 2:
				$result = 'Double ';

				break;
			default:
				throw new InvalidBuildException('Er... what?');
		}
		if (isset($this->_turnProduct)) {
			$result .= $this->_pledgeProduct.' Trick';
		} else {
			$result .= 'Fake '.$this->_pledgeProduct;
		}
		if ($this->_turnCount !== 0 && $this->_turnProduct !== null &&
			($this->_turnCount !== 1 || $this->_turnProduct->name !== 'Drone')) {
			$result .= ' into '.$this->_turnCount.' '.$this->_turnProduct.'s';
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function duration(): float {
		if (isset($this->_turnProduct)) {
			return $this->_turnProduct->timeCost;
		}

		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function gasCost(): int {
		return $this->_pledgeCount * $this->_pledgeProduct->gasCost +
			(isset($this->_turnProduct) ? ($this->_turnCount * $this->_turnProduct->gasCost) : 0);
	}

	/**
	 * {@inheritdoc}
	 */
	public function gasRefund(): int {
		return $this->_pledgeCount * (int)(3 * $this->_pledgeProduct->gasCost / 4);
	}

	/**
	 * {@inheritdoc}
	 */
	public function larvaCost(): int {
		return $this->_pledgeCount * $this->_pledgeProduct->larvaCost +
			(isset($this->_turnProduct) ? ($this->_turnCount * $this->_turnProduct->larvaCost) : 0);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mineralCost(): int {
		return $this->_pledgeCount * $this->_pledgeProduct->mineralCost +
			(isset($this->_turnProduct) ? ($this->_turnCount * $this->_turnProduct->mineralCost) : 0);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mineralRefund(): int {
		return $this->_pledgeCount * (int)(3 * $this->_pledgeProduct->mineralCost / 4);
	}

	/**
	 * {@inheritdoc}
	 */
	public function prerequisites(): array {
		return array_merge($this->_pledgeProduct->prerequisites, $this->_turnProduct->prerequisites ?? array());
	}

	/**
	 * {@inheritdoc}
	 */
	public function productsCreated(): array {
		return ($this->_turnCount !== 0 && isset($this->_turnProduct)) ?
			array_fill(0, $this->_turnCount, $this->_turnProduct) : array();
	}

	/**
	 * @return Race of this job
	 */
	public function race(): Race {
		return $this->_pledgeProduct->race;
	}

	/**
	 * {@inheritdoc}
	 */
	public function supplyCost($allowTrick = false): int {
		if ($allowTrick) {
			return $this->_pledgeCount * $this->_pledgeProduct->supplyCost +
				(isset($this->_turnProduct) ? ($this->_turnCount * $this->_turnProduct->supplyCost) : 0);
		}

		return isset($this->_turnProduct) ? ($this->_turnCount * $this->_turnProduct->supplyCost) : 0;
	}
}
