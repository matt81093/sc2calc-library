<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\error;

class StringParserException extends InvalidBuildException {
	/**
	 * @var int $lineNumber The number the parsing error occurred on
	 */
	public $lineNumber;

	public function __construct(int $lineNumber, string $message, string $description = null) {
		$this->lineNumber = $lineNumber;
		parent::__construct("Line '".($lineNumber)."' : {$message}", $description);
	}
}
