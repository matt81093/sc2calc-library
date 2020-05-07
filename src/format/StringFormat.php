<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\format;

use holonet\sc2calc\format\parser\Sc2CalcStringParser;

/**
 * The old sc2calc.org string based format.
 */
class StringFormat extends BuildFormat {
	/**
	 * {@inheritdoc}
	 */
	public static function description(): string {
		return 'sc2calc.org string based format';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function helpUrl(): string {
		return 'docs/format.md';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function name(): string {
		return 'string';
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function parserClass(): string {
		return Sc2CalcStringParser::class;
	}
}
