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
use holonet\sc2calc\sets\MutationSet;
use holonet\sc2calc\error\InvalidBuildException;

class CancelJob extends Job {
	/**
	 * @var Product $_cancelledProduct type of product of which to cancel recurring jobs
	 */
	private $_cancelledProduct;

	public function __construct(Product $cancelledProduct) {
		$this->_cancelledProduct = $cancelledProduct;
	}

	/**
	 * {@inheritdoc}
	 */
	public function cancel(array &$recurringJobs): void {
		$cancelled = false;
		foreach ($recurringJobs as $key => $recurringJob) {
			$recurringJobProduct = $recurringJob->productBuilt();
			if ($recurringJobProduct !== null && $recurringJobProduct->uid === $this->_cancelledProduct->uid) {
				unset($recurringJobs[$key]);
				$cancelled = true;
			}
		}
		if (!$cancelled) {
			throw new InvalidBuildException(
				'There is no recurring job for '.$this->_cancelledProduct->name.' to be cancelled.',
				"The cancel command can only be used to cancel recurring jobs, like '16 Marine [auto]'."
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Cancel '.(string)$this->_cancelledProduct;
	}

	/**
	 * {@inheritdoc}
	 */
	public function duration(): float {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mutations(): MutationSet {
		return new MutationSet();
	}
}
