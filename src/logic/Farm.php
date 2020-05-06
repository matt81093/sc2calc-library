<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\logic;

/**
 * A farm represents a product that increases supply capacity.
 */
class Farm {
	/**
	 * @var int $capacity Amount of supply capacity provided by this farm
	 */
	public $capacity;

	/**
	 * @var float $created Time when this farm was created
	 */
	public $created;

	/**
	 * @var float|null $destroyed Time when this farm was destroyed
	 */
	public $destroyed;

	public function __construct(float $created, int $capacity) {
		$this->capacity = $capacity;
		$this->created = $created;
	}

	/**
	 * Compare two farms by time created.
	 */
	public static function compare(self $farm1, self $farm2): int {
		return $farm1->created <=> $farm2->created;
	}
}
