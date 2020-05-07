<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\format;

use holonet\sc2calc\format\parser\Parser;
use holonet\sc2calc\sets\ProductsManager;
use Symfony\Component\Stopwatch\Stopwatch;
use holonet\sc2calc\error\InvalidFormatException;

/**
 * static base class used as a kind of "registry" for available formats.
 */
abstract class BuildFormat {
	/**
	 * @var class-string<BuildFormat>[] FORMATS static registry of available formats
	 */
	public const FORMATS = array(
		StringFormat::class
	);

	/**
	 * @return string with a short description of the format
	 */
	abstract public static function description(): string;

	/**
	 * @return string with a url to a document with help about this format
	 */
	abstract public static function helpUrl(): string;

	/**
	 * @return string with the identifying name of the format
	 */
	abstract public static function name(): string;

	/**
	 * Instantiate a parser for the given format.
	 * @param string $format The given format to parse
	 * @param Stopwatch $stopwatch Stopwatch component for profiling
	 * @param ProductsManager $productManager Reference to the product registry with all loaded products
	 * @return Parser instance for the given format
	 */
	public static function parser(string $format, Stopwatch $stopwatch, ProductsManager $productManager): Parser {
		$formatClass = static::format($format);
		/** @var class-string<Parser> $parserClass */
		$parserClass = $formatClass::parserClass();

		return new $parserClass($stopwatch, $productManager);
	}

	/**
	 * @psalm-return class-string<Parser>
	 */
	protected static function parserClass(): string {
		throw new InvalidFormatException('Format '.static::name().' does not support build order string parsing');
	}

	private static function format(string $format) {
		foreach (static::FORMATS as $formatClass) {
			if ($formatClass::name() === $format) {
				return $formatClass;
			}
		}

		throw new InvalidFormatException("Unknown format {$format}");
	}
}
