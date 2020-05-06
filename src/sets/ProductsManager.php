<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use Exception;
use RuntimeException;
use holonet\sc2calc\enum\Race;
use holonet\sc2calc\logic\Product;

/**
 * Used to keep track of the logic objects representing ingame products.
 * @see Product
 */
class ProductsManager {
	/**
	 * @var Product[] $all list of all exposed products
	 */
	public $all = array();

	/**
	 * @var Product[] $_designated list of designated products for specific types
	 */
	private $_designated = array();

	/**
	 * @var int $last_uid last unique identifier created
	 */
	private $last_uid = 0;

	/**
	 * Find exposed product by identifier.
	 * @throws Exception if the product cannot be found
	 */
	public function byIdentifier(string $identifier): Product {
		if (!isset($this->all[$identifier])) {
			throw new Exception("Could not find product by identifier '{$identifier}'");
		}

		return $this->all[$identifier];
	}

	/**
	 * Find exposed product by name.
	 * @return Product
	 */
	public function byName(string $name): ?Product {
		foreach ($this->all as $candidate) {
			if (strcasecmp($candidate->name, $name) === 0) {
				return $candidate;
			}
		}

		return null;
	}

	/**
	 * Get designated product of given type.
	 * @param Race $race The race to get a designated type for
	 * @param string $type The type to get below the specific race
	 */
	public function designated(Race $race, string $type): Product {
		$key = "{$race}-{$type}";
		if (!isset($this->_designated[$key])) {
			throw new Exception("Could not find product by designation '{$key}'");
		}

		return $this->_designated[$key];
	}

	/**
	 * Instantiation method loading in all json data for all products.
	 * @return ProductsManager with loaded data
	 */
	public static function load(): self {
		$me = new static();
		$dataPath = realpath(__DIR__.'/../../data');
		$validDataSets = array('structures', 'units', 'upgrades', 'abilities', 'morphs');

		foreach (Race::values() as $race) {
			if (!$race->isPlayable()) {
				continue;
			}

			$raceProducts = array();

			foreach ($validDataSets as $dataSet) {
				$jsonFilename = "{$dataPath}/".lcfirst($race->getValue())."/{$dataSet}.json";
				$jsonPath = realpath($jsonFilename);

				if ($jsonPath === false || !is_readable($jsonPath)) {
					throw new RuntimeException("Could not find / read data json file '{$jsonFilename}'");
				}

				$data = json_decode(file_get_contents($jsonPath), true);
				if (!is_array($data)) {
					throw new RuntimeException("Could not parse json data file '{$jsonFilename}': ".json_last_error_msg());
				}

				$raceProducts = array_merge($raceProducts, $data);
			}

			foreach ($raceProducts as $key => $product) {
				$product['race'] = $race;
				$me->loadProduct($key, $product);
			}
		}

		return $me;
	}

	/**
	 * @param string $identifier Identifier to refer to the product
	 * @param array $productDef Json-loaded product definition
	 * @throws Exception if the loaded json definitions are faulty
	 * @return Product object created from definition
	 */
	public function loadProduct(string $identifier, array $productDef): Product {
		$product = new Product($this, $productDef);

		if (isset($productDef['expends'])) {
			try {
				$product->expends = $this->selectByIdentifier($productDef['expends']);
			} catch (Exception $e) {
				throw new Exception("Invalid product definition: product '{$product->name}' expends: {$e->getMessage()}");
			}
		} else {
			$product->expends = null;
		}

		if (isset($productDef['prerequisites'])) {
			try {
				$product->prerequisites = $this->selectByIdentifier($productDef['prerequisites']);
			} catch (Exception $e) {
				throw new Exception("Invalid product definition: product '{$product->name}' prerequisites: {$e->getMessage()}");
			}
		} else {
			$product->prerequisites = array();
		}

		if (isset($productDef['yields'])) {
			try {
				$product->yields = $this->selectByIdentifier($productDef['yields']);
			} catch (Exception $e) {
				throw new Exception("Invalid product definition: product '{$product->name}' yields: {$e->getMessage()}");
			}
		} else {
			$product->yields = null;
		}

		// set uid & append to all
		$product->uid = $this->last_uid++;
		$this->all[$identifier] = $product;

		foreach (array('StartBase', 'Base', 'Worker', 'Geyser', 'Booster') as $designatedType) {
			if (in_array($designatedType, $product->types)) {
				$this->_designated["{$product->race}-{$designatedType}"] = $product;
			}
		}

		return $product;
	}

	/**
	 * Resolve an array of product identifiers to an array of product references.
	 * @param string[] $productIdentifiers Names of product to find
	 * @throws Exception if any of the products cannot be found
	 * @return Product[]
	 */
	public function selectByIdentifier(array $productIdentifiers): array {
		$ret = array();
		foreach ($productIdentifiers as $id) {
			$ret[] = $this->byIdentifier($id);
		}

		return $ret;
	}
}
