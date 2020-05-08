<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\cli;

use holonet\cli\Command;
use holonet\cli\Application;
use holonet\sc2calc\format\BuildFormat;

/**
 * Interact with / use the sc2calc library from the command line.
 */
class Sc2CalcApplication extends Application {
	/**
	 * {@inheritdoc}
	 */
	public function describe(): string {
		return 'Use the sc2calc build order calculator library from the command line';
	}

	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'sc2calc';
	}

	/**
	 * {@inheritdoc}
	 */
	public function version(): string {
		return '1.0.0';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure(): void {
		$this->argumentDefinition->addFlag('p|profiling', 'profiling', 'Print extended calculation profiling information');
		$this->argumentDefinition->addFlag('d|debug', 'debug', 'Log extended trace of all calculations')->default(false);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function loadCommands(): void {
		$this->addCommand(new commands\CalculateCommand());
	}

	/**
	 * {@inheritdoc}
	 * Overwritten so we can add a list of available formats to the.
	 */
	protected function usageText(bool $includeDescription = false): string {
		$usageText = parent::usageText($includeDescription)."Available build order string formats:\n";

		foreach (BuildFormat::FORMATS as $formatClass) {
			$usageText .= "  '{$formatClass::name()}': {$formatClass::description()} (see {$formatClass::helpUrl()})\n";
		}

		return $usageText;
	}
}
