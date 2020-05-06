<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\job;

use InvalidArgumentException;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\error\InvalidBuildException;

/**
 * Availability of a job at a given moment while scheduling.
 */
class Availability {
	public const AVAILABLE = 0;

	public const INSUFFICIENT_SUPPLY = 1;

	public const INSUFFICIENT_SUPPLY_CAPACITY = 2;

	public const MISSING_DEPENDENCY = 6;

	public const MISSING_PREREQUISITE = 7;

	public const MISSING_PRODUCTION_QUEUE = 8;

	public const MISSING_SPELLCASTER = 9;

	public const NO_GAS_PRODUCTION = 3;

	public const NO_LARVAE_PRODUCTION = 4;

	public const NO_MINERAL_PRODUCTION = 5;

	/**
	 * @var Job $missingDependency Previous job which is not scheduled
	 */
	public $missingDependency;

	/**
	 * @var Product $missingPrerequisite Product which has not already been built, but which is required for this job
	 */
	public $missingPrerequisite;

	/**
	 * @var array $missingQueues Products which represent production queues that this job needs, but which have not been built yet
	 */
	public $missingQueues;

	/**
	 * @var Product|null $missingSpellcaster Type of spellcaster required to execute this job, and which has not been built yet
	 */
	public $missingSpellcaster;

	/**
	 * @var int $status Status of availability
	 */
	public $status;

	/**
	 * @var int $supplyCount Current supply count
	 */
	public $supplyCount;

	/**
	 * @var int $supplyNeeded Supply count needed by this job's trigger
	 */
	public $supplyNeeded;

	/**
	 * @var array $tagsRequired list of tags that indicates which production queues can be used
	 */
	public $tagsRequired;

	public function __construct(int $status) {
		$this->status = $status;
	}

	/**
	 * @return string representation of this availability status
	 */
	public function __toString() {
		switch ($this->status) {
			case self::AVAILABLE:
				return '';

			case self::INSUFFICIENT_SUPPLY:
				return 'There is '.($this->supplyCount > $this->supplyNeeded ? 'too much' : 'insufficient').' supply.';

			case self::INSUFFICIENT_SUPPLY_CAPACITY:
				return 'There is insufficient supply capacity.';

			case self::NO_GAS_PRODUCTION:
				return 'No gas is being mined.';

			case self::NO_LARVAE_PRODUCTION:
				$result = 'No larva are being generated';
				$result .= (isset($this->tagsRequired) ? (' from a hatchery with tag'.(count($this->tagsRequired) > 1 ? 's' : '').' #'.implode(' or #', $this->tagsRequired)) : '').'.';

				return $result;

			case self::NO_MINERAL_PRODUCTION:
				return 'No minerals are being mined.';

			case self::MISSING_DEPENDENCY:
				return "The job '{$this->missingDependency}' on which it depends could not be scheduled.";

			case self::MISSING_PREREQUISITE:
				return "The prerequisite '{$this->missingPrerequisite}' does not exist.";

			case self::MISSING_PRODUCTION_QUEUE:
				$result = 'No production queue'.(count($this->missingQueues) > 1 ? 's' : '').
					' of type ';
				for ($i = 0; $i < count($this->missingQueues); $i++) {
					$result .= ($i > 0 ? (($i === count($this->missingQueues) - 1) ? ' and ' : ', ') : '').
						"'".$this->missingQueues[$i]."'";
				}
				$result .= (count($this->missingQueues) > 1 ? ' exist' : ' exists');
				$result .= (isset($this->tagsRequired) ? (' with tag'.(count($this->tagsRequired) > 1 ? 's' : '').' #'.implode(' or #', $this->tagsRequired)) : '').'.';

				return $result;

			case self::MISSING_SPELLCASTER:
				return "No spellcasters of type '{$this->missingSpellcaster}' exist".
					(isset($this->tagsRequired) ? (' with tag'.(count($this->tagsRequired) > 1 ? 's' : '').' #'.implode(' or #', $this->tagsRequired)) : '').'.';
			default:
				throw new InvalidArgumentException("Unknown Availability status constant '{$this->status}'");
		}
	}

	/**
	 * @return string with a descriptive text that helps the user understand the error
	 */
	public function description(): string {
		switch ($this->status) {
			case self::AVAILABLE:
			case self::MISSING_DEPENDENCY:
				return '';

			case self::INSUFFICIENT_SUPPLY:
				return "The trigger supply count for this job is {$this->supplyNeeded}, but at this point in the build order the achieved supply count is ".($this->supplyCount > $this->supplyNeeded ? 'already' : 'only')." {$this->supplyCount}.";

			case self::INSUFFICIENT_SUPPLY_CAPACITY:
				return 'You may need to add some Overlords, Supply Depots or Pylons to accommodate it.';

			case self::NO_GAS_PRODUCTION:
				return "Usually, this means that you didn't put workers on gas. It could also be that you took workers off gas before enough gas was gathered. To put workers on gas when you build an assimilator, write '12 Assimilator > transfer 3 workers' or '12 Assimilator > +3'. Similarly for a Refinery or an Extractor.";

			case self::NO_LARVAE_PRODUCTION:
				return 'This error message should not occur. Please report this message with your build order on the thread linked at bottom of the page.';

			case self::NO_MINERAL_PRODUCTION:
				return 'You may have taken all remaining workers off minerals, or used up all your Drones to build structures.';

			case self::MISSING_PREREQUISITE:
				return 'You must ensure that the prerequisite structure or upgrade can be scheduled before this job.';

			case self::MISSING_PRODUCTION_QUEUE:
				return 'You must ensure that the required production queue exists before this job.';

			case self::MISSING_SPELLCASTER:
				return 'You must ensure that the required spellcaster exists before this job.';
			default:
				throw new InvalidArgumentException("Unknown Availability status constant '{$this->status}'");
		}
	}

	/**
	 * Determine if the given job could solve the reason of this unavailability.
	 * @return bool True, if the given job can actively contribute to making
	 *              this job available
	 */
	public function solvedBy(Job $job): bool {
		switch ($this->status) {
			case self::AVAILABLE:
				return true;

			// job must affect supply
			case self::INSUFFICIENT_SUPPLY:
				$supplyGap = $this->supplyNeeded - $this->supplyCount;
				if ($supplyGap > 0) {
					return $job->supplyCost(true) <= $supplyGap && $job->supplyCost(true) > 0;
				}
				if ($supplyGap < 0) {
					return $job->supplyCost(true) >= $supplyGap && $job->supplyCost(true) < 0;
				}

				return true;

			// job must increase supply capacity
			case self::INSUFFICIENT_SUPPLY_CAPACITY:
				$productsCreated = $job->productsCreated();
				if ($productsCreated !== null) {
					foreach ($productsCreated as $product) {
						if ($product !== null && $product->supplyCapacity > 0) {
							return true;
						}
					}
				}

				return false;

			case self::NO_GAS_PRODUCTION:
			case self::NO_LARVAE_PRODUCTION:
			case self::NO_MINERAL_PRODUCTION:
			case self::MISSING_DEPENDENCY:
			case self::MISSING_PREREQUISITE:
			case self::MISSING_PRODUCTION_QUEUE:
			case self::MISSING_SPELLCASTER:
				return false;
			default:
				throw new InvalidBuildException('Job was unavailable, but no reason was specified.');
		}
	}
}
