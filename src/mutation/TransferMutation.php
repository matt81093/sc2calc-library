<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\mutation;

use LogicException;
use holonet\sc2calc\logic\IncomeSlot;

/**
 * Mutation that transfers workers from one resource to another.
 */
class TransferMutation extends Mutation {
	/**
	 * {@inheritdoc}
	 */
	public function __toString(): string {
		return
			(empty($this->mineralChange) ? '' : ('Transfer '.$this->mineralChange.' workers to new base')).
			(empty($this->gasChange) ? '' : ('Transfer '.$this->gasChange.' workers to new geyser'));
	}

	/**
	 * Choose distribution of workers taken off or put on resources per geyser
	 * or base, based on the given income slot. This distribution is then
	 * solidified, and applied to every subsequent income slot.
	 */
	public function distribute(IncomeSlot $slot): void {
		if (isset($this->_gasNegativeChange) || isset($this->_mineralNegativeChange)) {
			return;
		}

		if (!empty($this->mineralChange)) {

			// single base
			if (count($slot->mineralMiners) === 1) {
				throw new LogicException('Cannot transfer workers if you have only one base.');

				// multiple bases
			}
			$mineralChange = array_fill(0, count($slot->mineralMiners), 0);

			// take miners off minerals
			$left = $this->mineralChange;
			do {
				$mostSaturated = 0;
				for ($i = 1; $i < count($slot->mineralMiners) - 1; $i++) {
					if ($slot->mineralMiners[$i] + $mineralChange[$i] > $slot->mineralMiners[$mostSaturated] + $mineralChange[$mostSaturated]) {
						$mostSaturated = $i;
					}
				}
				$mineralChange[$mostSaturated]--;
			} while (--$left > 0);

			// put miners on minerals at new base
			$mineralChange[count($mineralChange) - 1] += $this->mineralChange;

			// store changes
			$this->storeMineralChanges($mineralChange);
		}

		// transfer N miners to new geyser from other geysers
		if (!empty($this->gasChange)) {

			// single geyser
			if (count($slot->gasMiners) === 1) {
				throw new LogicException('Cannot transfer workers if you have only one geyser.');

				// multiple geysers
			}
			$gasChange = array_fill(0, count($slot->gasMiners), 0);

			// take miners off gas
			$left = $this->gasChange;
			do {
				$mostSaturated = 0;
				for ($i = 1; $i < count($slot->gasMiners) - 1; $i++) {
					if ($slot->gasMiners[$i] + $gasChange[$i] > $slot->gasMiners[$mostSaturated] + $gasChange[$mostSaturated]) {
						$mostSaturated = $i;
					}
				}
				$gasChange[$mostSaturated]--;
			} while (--$left > 0);

			// put miners on gas at new geyser
			$gasChange[count($gasChange) - 1] += $this->gasChange;

			// store changes
			$this->storeGasChanges($gasChange);
		}
	}
}
