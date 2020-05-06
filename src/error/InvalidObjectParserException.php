<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\error;

class InvalidObjectParserException extends StringParserException {
	public function __construct(int $lineNumber, string $object, string $invalidProduct) {
		parent::__construct(
			$lineNumber,
			"Unknown {$object} '{$invalidProduct}'",
			'Please refer to the complete list of units, structures, upgrades, morphs and abilities. If you are trying to do something other than building, please check the single line examples for the syntax of the other commands. The syntax is not case-sensitive, but it is very specific in the spelling.'
		);
	}
}
