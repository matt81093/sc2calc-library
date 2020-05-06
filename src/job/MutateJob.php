<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

use holonet\sc2calc\sets\MutationSet;
use holonet\sc2calc\mutation\Mutation;

class MutateJob extends Job {
	/**
	 * @var Mutation $_mutation Mutation associated with a mutation job
	 */
	private $_mutation;

	public function __construct(Mutation $mutation) {
		$this->_mutation = $mutation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function consumptive(): bool {
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return (string)$this->_mutation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function duration(): float {
		return $this->_mutation->delay;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mutations(): MutationSet {
		$mutations = new MutationSet();
		$mutations->add($this->_mutation, $this->timeStarted);

		return $mutations;
	}

	/**
	 * {@inheritdoc}
	 */
	public function when($income): ?float {
		return $this->_mutation->when($this->timeStarted, $income);
	}
}
