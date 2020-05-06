<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\sets;

use Iterator;
use Countable;
use holonet\sc2calc\mutation\Mutation;

/**
 * Set of mutations.
 */
class MutationSet implements Countable, Iterator {
	/**
	 * @var Mutation[] $_mutations list of mutations
	 */
	private $_mutations = array();

	/**
	 * @var int $_position part of Iterator implementation
	 */
	private $_position;

	/**
	 * @param Mutation $mutation to be added to the list
	 * @param float $time
	 */
	public function add($mutation, $time): void {
		$mutation->time = $time;
		$this->_mutations[] = $mutation;
	}

	/// Countable implementation
	public function count(): int {
		return count($this->_mutations);
	}

	/// Iterator implementation
	public function current(): Mutation {
		return $this->_mutations[$this->_position];
	}

	public function key(): int {
		return $this->_position;
	}

	public function next(): void {
		++$this->_position;
	}

	public function rewind(): void {
		$this->_position = 0;
	}

	/**
	 * Sort mutations by time.
	 */
	public function sort(): void {
		usort($this->_mutations, array(Mutation::class, 'compare'));
	}

	public function valid(): bool {
		return isset($this->_mutations[$this->_position]);
	}
}
