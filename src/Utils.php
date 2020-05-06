<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc;

use InvalidArgumentException;

/**
 * Class Utils to contain static utility methods.
 */
class Utils {
	/**
	 * Convenience wrapper around max().
	 * @return float the bigger value of the two
	 */
	public static function floatmax($floatIshValOne, $floatishValTwo): float {
		if ($floatIshValOne === null) {
			return $floatishValTwo ?? INF;
		}

		if ($floatishValTwo === null) {
			return $floatIshValOne ?? INF;
		}

		if (!is_numeric($floatIshValOne) || !is_numeric($floatishValTwo)) {
			dd($floatishValTwo, $floatIshValOne);

			throw new InvalidArgumentException('Floatmax can only be called with two numeric values');
		}

		return max((float)$floatIshValOne, (float)$floatishValTwo);
	}

	/**
	 * Convert time in seconds to a string of the form "m:ss". Special cases are if
	 * given time is not numeric, or if given time is infinite.
	 * @param float $seconds
	 */
	public static function simple_time(?float $seconds): string {
		if (!is_numeric($seconds)) {
			return 'NaN';
		}
		if ($seconds === INF) {
			return '∞';
		}

		return ($seconds < 0 ? '-' : '').floor(abs($seconds) / 60).':'.str_pad((string)round(abs($seconds) % 60), 2, '0', STR_PAD_LEFT);
	}
}
