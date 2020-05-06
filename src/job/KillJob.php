<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

use holonet\sc2calc\logic\Product;

class KillJob extends Job {
	/**
	 * @var Product $_product product to be killed by this job
	 */
	private $_product;

	public function __construct(Product $product) {
		$this->_product = $product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Kill '.(string)$this->_product;
	}

	/**
	 * {@inheritdoc}
	 */
	public function productsDestroyed(): array {
		return array($this->_product);
	}

	/**
	 * {@inheritdoc}
	 */
	public function supplyCost($allowTrick = false): int {
		return -$this->_product->supplyCost;
	}
}
