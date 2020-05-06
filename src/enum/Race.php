<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\enum;

use LogicException;
use MyCLabs\Enum\Enum;
use holonet\sc2calc\init\Initialiser;
use holonet\sc2calc\init\ZergInitialiser;
use holonet\sc2calc\init\ProtossInitialiser;

/**
 * @method static Race PROTOSS()
 * @method static Race TERRAN()
 * @method static Race ZERG()
 * @method static Race RANDOM()
 * @psalm-immutable
 */
class Race extends Enum {
	private const PROTOSS = 'Protoss';

	private const RANDOM = 'Random';

	private const TERRAN = 'Terran';

	private const ZERG = 'Zerg';

	/**
	 * @psalm-return class-string<Initialiser>
	 * @return string with the initialiser class to be used
	 */
	public function initialiser(): string {
		if (!$this->isPlayable()) {
			throw new LogicException('Cannot initialise a timeline using an unplayable race');
		}

		switch ($this->value) {
			case static::PROTOSS:
				return ProtossInitialiser::class;
			case static::ZERG:
				return ZergInitialiser::class;
			default:
				return Initialiser::class;
		}
	}

	public function isPlayable() {
		return $this->value !== static::RANDOM;
	}
}
