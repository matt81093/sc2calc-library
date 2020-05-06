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
use holonet\sc2calc\sets\IncomeSlots;
use holonet\sc2calc\sets\MutationSet;
use holonet\sc2calc\mutation\Mutation;
use holonet\sc2calc\mutation\MULEMutation;
use holonet\sc2calc\mutation\BaseStartedMutation;
use holonet\sc2calc\mutation\BaseCompletedMutation;
use holonet\sc2calc\mutation\GeyserStartedMutation;
use holonet\sc2calc\mutation\GeyserCompletedMutation;

/**
 * Jobs are the basic components of a build order. They represent, for example,
 * the construction of a unit, structure, or a mutation in income.
 */
abstract class Job {
	/**
	 * @var Availability $availability Availability of this job
	 */
	public $availability;

	/**
	 * @var int $chronoboost Number of chronoboosts to use on this job
	 */
	public $chronoboost;

	/**
	 * @var Dependency $dependency Previous job which must be scheduled before this one
	 */
	public $dependency;

	/**
	 * @var int $initiateGas Amount of gas at which to initiate this job, i.e. send the worker early
	 */
	public $initiateGas;

	/**
	 * @var int $initiateMineral Amount of mineral at which to initiate this job, i.e. send the worker early
	 */
	public $initiateMineral;

	/**
	 * @var int $pickOrder Order in which the jobs were picked up by the scheduler
	 */
	public $pickOrder;

	/**
	 * @var Product $queueTypeExpended additional queue type expended by this job
	 */
	public $queueTypeExpended;

	/**
	 * @var bool $recurring If true, this job will be scheduled repeatedly until cancelled
	 */
	public $recurring = false;

	/**
	 * @var string $tag Tag that can be referred to by other jobs
	 */
	public $tag;

	/**
	 * @var string[] $tagsRequired Tags of queues or spellcasters that can be used to perform this job
	 */
	public $tagsRequired;

	/**
	 * @var float $timeCompleted Time when the job is completed
	 */
	public $timeCompleted = INF;

	/**
	 * @var float $timeInitiated Time when the job is initiated, i.e. when the worker is dispatched
	 */
	public $timeInitiated = INF;

	/**
	 * @var float $timeStarted Time when the job is started
	 */
	public $timeStarted = INF;

	/**
	 * @var int $triggerGas Amount of gas that triggers the start of the job
	 */
	public $triggerGas;

	/**
	 * @var int $triggerMineral Amount of mineral that triggers the start of the job
	 */
	public $triggerMineral;

	/**
	 * @var int $triggerSupply Amount of supply that triggers the start of the job
	 */
	public $triggerSupply;

	/**
	 * @var int $type Type of job, see class constants
	 */
	public $type;

	/**
	 * @return string representation of this job
	 */
	public function __toString(): string {
		return
			(isset($this->triggerGas) ? ('@'.$this->triggerGas.' gas ') : '').
			(isset($this->triggerMineral) ? ('@'.$this->triggerMineral.' minerals ') : '').
			(isset($this->triggerSupply) ? ($this->triggerSupply.' ') : '').
			$this->description().
			($this->recurring ? ' [auto]' : '');
	}

	/**
	 * Indicates whether any production queues expended by the job must be
	 * tagged as busy.
	 */
	public function busiesQueues(): bool {
		return false;
	}

	/**
	 * Cancel all recurring jobs that are targeted by this job.
	 * @param Job[] $recurringJobs
	 */
	public function cancel(array &$recurringJobs): void {
	}

	/**
	 * @return bool indicating whether the job consumes any scarce resources, including time
	 */
	public function consumptive(): bool {
		return true;
	}

	/**
	 * @return string short description of this job
	 */
	abstract public function description(): string;

	/**
	 * @return float get the duration of the job
	 */
	public function duration(): float {
		return 0;
	}

	/**
	 * @return int get energy cost of this job
	 */
	public function energyCost(): int {
		return 0;
	}

	/**
	 * @return int get gas cost of this job
	 */
	public function gasCost(): int {
		return 0;
	}

	/**
	 * @return int amount of gas refunded after this job is completed
	 */
	public function gasRefund(): int {
		return 0;
	}

	/**
	 * @return int larva cost of this job
	 */
	public function larvaCost(): int {
		return 0;
	}

	/**
	 * @return int mineral cost of this job
	 */
	public function mineralCost(): int {
		return 0;
	}

	/**
	 * @return int amount of mineral refunded after this job is completed
	 */
	public function mineralRefund(): int {
		return 0;
	}

	public function morph(): bool {
		return false;
	}

	/**
	 * @return MutationSet all mutations to income that are caused by this job
	 */
	public function mutations(): MutationSet {
		// mutations caused by created products
		$mutations = new MutationSet();

		if ($this->productsCreated() !== null) {
			foreach ($this->productsCreated() as $product) {
				if ($product === null) {
					continue;
				}

				// worker is produced
				if (in_array('Worker', $product->types)) {
					$mutations->add(new Mutation(1, 0), $this->timeCompleted);
				}

				// new base is produced
				if (in_array('Base', $product->types)) {
					$mutations->add(new BaseStartedMutation(), $this->timeStarted);
					$mutations->add(new BaseCompletedMutation(), $this->timeCompleted);
				}

				// new geyser is developed
				if (in_array('Geyser', $product->types)) {
					$mutations->add(new GeyserStartedMutation(), $this->timeStarted);
					$mutations->add(new GeyserCompletedMutation(), $this->timeCompleted);
				}

				// MULE
				if ($product->name === 'Calldown: MULE') {
					$mutations->add(new MULEMutation(1), $this->timeStarted);
					$mutations->add(new MULEMutation(-1), $this->timeCompleted);
				}
			}

			$mutations->sort();
		}

		return $mutations;
	}

	/**
	 * @return Product[] list of prerequisite structures and upgrades for this job
	 */
	public function prerequisites(): array {
		return array();
	}

	/**
	 * @todo Deprecate this.
	 */
	public function productBuilt(): ?Product {
		return null;
	}

	/**
	 * @return Product[] list of products created by this job
	 */
	public function productsCreated(): array {
		return array();
	}

	/**
	 * @return Product[] list of products destroyed by this job
	 */
	public function productsDestroyed(): array {
		return array();
	}

	/**
	 * @return Product[] list of production queue types created by this job
	 */
	public function queueTypesCreated(): array {
		return array();
	}

	/**
	 * Get list of expended queue types.
	 * @psalm-return array{Product[], bool}|null
	 */
	public function queueTypesExpended(): ?array {
		if (isset($this->queueTypeExpended)) {
			return array(array($this->queueTypeExpended), false);
		}

		return null;
	}

	/**
	 * @return Race|null null as a base job cannot have a race
	 */
	public function race(): ?Race {
		return null;
	}

	/**
	 * @return Product expended spellcaster type
	 */
	public function spellcasterTypeExpended(): ?Product {
		return null;
	}

	/**
	 * Get supply cost of this job.
	 * @param bool $allowTrick If true, don't report supply cost for tricks
	 */
	public function supplyCost(bool $allowTrick = false): int {
		return 0;
	}

	/**
	 * Calculate earliest time when income allows this job to be performed.
	 * @return float
	 */
	public function when(IncomeSlots $income): ?float {
		return 0;
	}
}
