<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\error;

use LogicException;

class InvalidBuildException extends LogicException {
	/**
	 * @var string|null $description Detailed description of the build entry error
	 */
	public $description;

	public function __construct(string $message, string $description = null) {
		$this->description = $description;
		parent::__construct($message);
	}
}
