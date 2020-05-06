<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

use Exception;
use InvalidArgumentException;
use holonet\sc2calc\enum\Race;
use holonet\sc2calc\sets\ProductsManager;

/**
 * Products are structures, units, upgrades, morphs, addons, addon swaps,
 * abilities, etc. Basically, anything that is buildable in the game.
 */
class Product {
	/**
	 * @var int $energyCost energy cost of this product
	 */
	public $energyCost = 0;

	/**
	 * @var int $energyMax maximum energy on this spellcaster
	 */
	public $energyMax = 0;

	/**
	 * @var int $energyStart initial energy on this spellcaster
	 */
	public $energyStart = 0;

	/**
	 * @var Product[]|null $expends production queues expended to build this product
	 */
	public $expends = array();

	/**
	 * @var bool $expendsAll if true, all production queues are required; if false, only one of them
	 */
	public $expendsAll = false;

	/**
	 * @var int $gasCost gas cost of this product
	 */
	public $gasCost = 0;

	/**
	 * @var int $larvaCost larva cost of this product
	 */
	public $larvaCost = 0;

	/**
	 * @var ProductsManager $manager reference to the manager of products
	 */
	public $manager;

	/**
	 * @var int $mineralCost mineral cost of this product
	 */
	public $mineralCost = 0;

	/**
	 * @var string $name name of this product
	 */
	public $name = 'Placeholder';

	/**
	 * @var Product[] $prerequisites prerequisite structures or upgrades to build this product
	 */
	public $prerequisites = array();

	/**
	 * @var Race $race race of this product
	 */
	public $race;

	/**
	 * @var Product $spellCaster type of spellcaster needed to use this ability
	 */
	public $spellCaster;

	/**
	 * @var float $spellCooldown time until Ability can be used again
	 */
	public $spellCooldown = 0.0;

	/**
	 * @var int $supplyCapacity supply capacity provided by this product
	 */
	public $supplyCapacity = 0;

	/**
	 * @var int $supplyCost supply cost of this product
	 */
	public $supplyCost = 0;

	/**
	 * @var float $timeCost time it takes to complete this product
	 */
	public $timeCost = 0.0;

	/**
	 * @var string[] $types types of this product
	 */
	public $types = array();

	/**
	 * @var int $uid unique identifier of this product
	 */
	public $uid;

	/**
	 * @var Product[]|null $yields for a morph, a list of products that are yielded by the morph
	 */
	public $yields = array();

	public function __construct(ProductsManager $manager, array $product) {
		$this->manager = $manager;
		if (empty($product)) {
			throw new InvalidArgumentException('Attempted to construct empty object.');
		}
		$this->race = new Race($product['race']);
		$this->name = $product['name'];
		$this->types = $product['type'];

		//optional fields
		$this->timeCost = $product['cost']['time'] ?? 0;
		$this->mineralCost = $product['cost']['minerals'] ?? 0;
		$this->gasCost = $product['cost']['gas'] ?? 0;
		$this->supplyCost = $product['cost']['supply'] ?? 0;
		$this->supplyCapacity = $product['supplyCapacity'] ?? 0;

		if (in_array('Unit', $this->types)) {
			if ($this->race->equals(Race::ZERG())) {
				$this->larvaCost = $product['cost']['larva'] ?? (in_array('Structure', $this->types) ? 0 : 1);
			}
		}

		if (in_array('Ability', $this->types)) {
			$this->spellCaster = $this->manager->byIdentifier($product['spellCaster']);
			$this->energyCost = $product['cost']['energy'];
			$this->spellCooldown = $product['spellCooldown'];
		}

		if (in_array('Morph', $this->types)) {
			$this->expendsAll = true;
		}

		if (in_array('Spellcaster', $this->types)) {
			if (!isset($product['energyStart']) || !isset($product['energyMax'])) {
				throw new Exception("Invalid data definition: Spellcaster '{$this->name}' is missing 'energyStart' and / or 'energyMax");
			}
			$this->energyStart = $product['energyStart'];
			$this->energyMax = $product['energyMax'];
		}
	}

	/**
	 * @return string representation
	 */
	public function __toString(): string {
		return isset($this->name) ? $this->name : 'n/a';
	}

	/**
	 * When a product is unset.
	 */
	public function drop(): void {
		foreach ($this->manager->all as &$candidate) {
			if ($this->uid === $candidate->uid) {
				unset($candidate);

				break;
			}
		}
	}
}
