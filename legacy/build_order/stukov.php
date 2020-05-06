<?php

// This file is part of sc2calc.org - http://sc2calc.org/
//
// sc2calc.org is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// sc2calc.org is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with sc2calc.org. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package sc2calc.org
 * @copyright 2010 Jasper Abraham Visser
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A infestedcc is any structure that produces larvae.
 */
class InfestedCC {

	/// public members

	/**
	 * Time when infestedcc was completed
	 * @var float
	 */
	public $created;

	/**
	 * Initial number of larvae on this infestedcc
	 * @var int
	 */
	public $initialLarvae;

	/**
	 * Number of larvae currently on this infestedcc
	 * @var int
	 */
	public $larvae = 0;

	/**
	 * Time when next larva will be generated
	 * @var float
	 */
	public $nextLarvaGenerated;

	/**
	 * Number that indicates in which order the infestedccs were created
	 * @var int
	 */
	public $order;

	/**
	 * Tag to reference this specific infestedcc
	 * @var string
	 */
	public $tag;

	/**
	 * Rebate on time required to generate next larva.
	 * @var float
	 */
	public $timeRebate = 0;

	/**
	 * For each vomit, time when it expires
	 * @var array
	 */
	public $vomitExpires = array();
	
	/// private members

	/**
	 * For each larva, time when it was generated
	 * @var array
	 */
	private $_generated = array();

	/**
	 * Time when infestedcc was last updated
	 * @var float
	 */
	private $_lastUpdated;

	/// constructor

	/**
	 * Create new infestedcc.
	 * @param float $created
	 * @param int $initialLarvae
	 */
	public function __construct($created, $initialLarvae = 3, $tag = null) {
		$this->created = $created;
		$this->initialLarvae = $initialLarvae;
		$this->tag = $tag;
		$this->generateLarvae($this->created, $this->initialLarvae);
		$this->_lastUpdated = $this->created;
	}

	/// operators

	/**
	 * Convert to a string.
	 * @return string
	 */
	public function __tostring() {
		$result = "<tr>".
			"<td>". $this->order ."</td>".
			"<td class=\"center\">". simple_time($this->created) ."</td>".
			"<td class=\"center\">";
		$i = 0;
		foreach($this->_generated as $generated) {
			$result .= ($i > 0 ? (($i % 10 == 0) ? "<br/>" : " &gt; ") : "") . simple_time($generated);
			$i++;
		}
		$result .= "</tr>";
		return $result;
	}

	/// public methods

	/**
	 * Generate given number of larvae at the given time.
	 * @param float $time
	 * @param int $number
	 * @param bool $resetGeneration
	 */
	public function generateLarvae($time, $number = 1, $resetGeneration = true) {
		for($i = 0; $i < $number; $i++) {
			$this->_generated[] = $time;
		}
		$this->larvae += $number;
		$this->larvae = min(19, $this->larvae);
		if($resetGeneration) {
			$this->nextLarvaGenerated = $time + STUKOV_CC_LARVA_RATE;
			$this->timeRebate = 0;
			if(Hatcheries::$debug) tracemsg("Hatcheries[". $this->order ."]::generateLarvae(), at ". simple_time($time) ." we generate a larva. The next larva will be generated at ". simple_time($this->nextLarvaGenerated) .". This infestedcc now has ". $this->larvae ." larvae.");
		} elseif($this->larvae > 2) {
			$this->timeRebate = $time - $this->nextLarvaGenerated + STUKOV_CC_LARVA_RATE;
			if(Hatcheries::$debug) tracemsg("Hatcheries[". $this->order ."]::generateLarvae(), at ". simple_time($time) ." we rise above 2 larvae. The next larva would be generated at ". simple_time($this->nextLarvaGenerated) .", so the rebate is set at ". $this->timeRebate ." seconds.");
		}
	}

	/**
	 * Time when next larva is generated, infinite if there are 3 or more larvae
	 * available.
	 * @return float
	 */
	public function nextGenerated() {
		return $this->larvae < 3 ? $this->nextLarvaGenerated : INF;
	}

	/**
	 * Get time when next vomit expires, INF if none.
	 * @return float
	 */
	public function nextVomit() {
		if(count($this->vomitExpires) > 0) {
			return min($this->vomitExpires);
		}
		return INF;
	}

	/**
	 * Calculate surplus number of larvae at a given time in the future.
	 * @param float $time
	 * @return int
	 */
	public function surplus($time) {
		if(Hatcheries::$debug) tracemsg("Hatchery::surplus(". simple_time($time) .")");
		$infestedcc = clone $this;
		$infestedcc->update($time, false);
		return $infestedcc->larvae;
	}

	/**
	 * Update the state of this infestedcc up to given time.
	 * @param float $time
	 * @param bool $debug
	 */
	public function update($time) {
		if(Hatcheries::$debug) tracemsg("Hatchery::update(". simple_time($time) .")");

		if(Hatcheries::$debug) tracemsg("Hatchery::update(), nextGenerated=". simple_time($this->nextGenerated()) .", nextVomit=". simple_time($this->nextVomit()));
		while($this->nextGenerated() <= $time || $this->nextVomit() <= $time) {
			
			// expire vomits
			foreach($this->vomitExpires as $key => $vomitExpire) {
				if($vomitExpire <= min($time, $this->nextGenerated())) {
					if(Hatcheries::$debug) tracemsg("Hatchery::update(), expiring vomit at ". simple_time($vomitExpire));
					unset($this->vomitExpires[$key]);
					$this->generateLarvae($vomitExpire, 4, false);
				}
			}

			// generate larvae
			if($this->nextGenerated() <= $time) {
				if(Hatcheries::$debug) tracemsg("Hatchery::update(), generating larva at ". simple_time($this->nextLarvaGenerated));
				$this->generateLarvae($this->nextLarvaGenerated);
			}
		}

		$this->_lastUpdated = max($this->created, $time);
	}

	/**
	 * Queue vomit on this infestedcc at the given time.
	 * @global Product $SpawnLarvae
	 * @param float $time
	 */
	public function vomit($time) {
		global $SpawnLarvae;
		if(Hatcheries::$debug) tracemsg("Hatchery::vomit(". simple_time($time) .")");
		$this->vomitExpires[] = $time + $SpawnLarvae->timeCost;
		sort($this->vomitExpires);
	}

	/**
	 * Calculate time when the next larva is available.
	 * @return float
	 */
	public function when() {
		if($this->larvae > 0) {
			return $this->_lastUpdated;
		}
		return min($this->nextLarvaGenerated, $this->nextVomit());
	}

	/**
	 * Calculate time when another vomit can be queued on this infestedcc.
	 * @return float
	 */
	public function whenVomit() {
		return count($this->vomitExpires) > 0 ? max($this->vomitExpires) : $this->created;
	}
};

/**
 * The set of infestedccs available.
 */
class InfestedCCs {

	/// class constants
	const debugFlag = 32;

	/// static public members

	/**
	 * If true, will echo debug messages
	 * @var bool
	 */
	static public $debug = false;

	/// private members

	/**
	 * List of infestedccs
	 * @var array
	 */
	private $_infestedccs = array();

	/**
	 *
	 * @var bool
	 */
	private $_isClone = false;

	/**
	 * Time when infestedccs were last updated
	 * @var float
	 */
	private $_lastUpdated;

	/// constructor
	
	/**
	 * Create a copy of this.
	 */
	public function  __clone() {
		$infestedccs = array();
		foreach($this->_infestedccs as $infestedcc) {
			$infestedccs[] = clone $infestedcc;
		}
		$this->_infestedccs = $infestedccs;
		$this->_isClone = true;
	}

	/// operators

	/**
	 * Convert to a string.
	 * @return string
	 */
	public function __tostring() {
		return "<table id=\"pool\" class=\"display\" cellpadding=0 cellspacing=0>".
			"<thead>".
				"<tr>".
					"<th>#</th>".
					"<th>Created</th>".
					"<th>Larvae generated at</th>".
				"</tr>".
			"</thead>".
			"<tbody>".
				implode("", $this->_infestedccs) .
			"</tbody>".
		"</table>";
	}

	/// public methods

	/**
	 * Add a infestedcc to the list.
	 * @param Hatchery $infestedcc
	 */
	public function add($infestedcc) {
		$this->_infestedccs[] = $infestedcc;
		$infestedcc->order = count($this->_infestedccs);
	}

	/**
	 * Use up a single larva from any infestedcc that has one available and that
	 * has the required tags at the given time.
	 * @param float $time
	 * @param int $larvae
	 * @param array $tagsRequired
	 */
	public function expend($time, $larvae, $tagsRequired = null) {
		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::expend(". simple_time($time) .")");

		// update all
		$this->update($time);
		for($i = 0; $i < $larvae; $i++) {

			// choose infestedcc
			foreach($this->select($tagsRequired) as $infestedcc) {
				if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::expend(), infestedcc created at ". simple_time($infestedcc->created) ." has ". $infestedcc->larvae ." larvae.");
				if($infestedcc->larvae > 0 && $infestedcc->created <= $time) {
					if(!isset($candidate)) {
						$candidate = $infestedcc;
					} elseif($infestedcc->larvae > $candidate->larvae) {
						$candidate = $infestedcc;
					} elseif($infestedcc->larvae == $candidate->larvae && $infestedcc->nextVomit() < $candidate->nextVomit()) {
						$candidate = $infestedcc;
					} elseif($infestedcc->larvae == $candidate->larvae && $infestedcc->nextVomit() == $candidate->nextVomit() && $infestedcc->nextLarvaGenerated < $candidate->nextLarvaGenerated) {
						$candidate = $infestedcc;
					}
				}
			}
			if(!isset($candidate)) {
				throw_error("No infestedccs have larvae available at ". simple_time($time) .".",
					"This error message should not occur. Please report this message with your build order on the thread linked at bottom of the page.");
			}

			// reset time next larva is generated
			if($candidate->larvae == 3) {
				$candidate->nextLarvaGenerated = $time + STUKOV_CC_LARVA_RATE - $candidate->timeRebate;
				if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::expend(), at ". simple_time($time) ." we drop below 3 larvae. The rebate is ". $candidate->timeRebate ." seconds, so the next larva is generated at ". simple_time($candidate->nextLarvaGenerated));
			}

			// use larva
			if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries[". $candidate->order ."]::larvae-- = ". ($candidate->larvae - 1));
			$candidate->larvae--;
		}

		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::expend(), done!");
	}

	/**
	 * Find all infestedccs with one of the given tags.
	 * @param string $tagsRequired
	 * @return array Array of references to the infestedccs
	 */
	public function select($tagsRequired = null) {
		$infestedccs = array();
		foreach($this->_infestedccs as $infestedcc) {
			if($tagsRequired === null || (isset($infestedcc->tag) && in_array($infestedcc->tag, $tagsRequired))) {
				$infestedccs[] = $infestedcc;
			}
		}
		return $infestedccs;
	}

	/**
	 * Calculate surplus numbers of larvae on all infestedccs that have the
	 * required tags at a given time in the future.
	 * @param float $time
	 * @param array $tagsRequired
	 * @return array
	 */
	public function surplus($time, $tagsRequired = null) {
		$larvae = array();
		foreach($this->select($tagsRequired) as $infestedcc) {
			if($infestedcc->created <= $time) {
				$larvae[] = $infestedcc->surplus($time);
			}
		}
		return $larvae;
	}

	/**
	 * Update the state of all infestedccs up to given time.
	 * @param float $time
	 */
	public function update($time) {
		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::update(". simple_time($time) .")");

		if($time < $this->_lastUpdated) {
			throw_error("Cannot generate larvae in the past.",
				"This error message should not occur. Please report this message with your build order on the thread linked at bottom of the page.");
		}
		
		// generate larvae
		foreach($this->_infestedccs as $infestedcc) {
			$infestedcc->update($time, self::$debug);
		}

		$this->_lastUpdated = $time;
	}

	/**
	 * Queue use of vomit at given time in the future.
	 * @param float $time
	 */
	public function vomit($time) {

		// choose infestedcc
		foreach($this->_infestedccs as $infestedcc) {
			if($infestedcc->created <= $time) {
				if(!isset($candidate)) {
					$candidate = $infestedcc;
				} elseif($infestedcc->whenVomit() < $candidate->whenVomit()) {
					$candidate = $infestedcc;
				}
			}
		}

		// queue vomit
		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::vomit(), vomitting to infestedcc created at ". simple_time($candidate->created));
		$candidate->vomit($time);
	}

	/**
	 * Calculate time when infestedccs with the required tags has the given
	 * number of free larva.
	 * @param int $larvae
	 * @param array $tagsRequired
	 * @return float
	 */
	public function when($larvae, $tagsRequired = null) {
		$time = INF;
		if($larvae == 1) {
			foreach($this->select($tagsRequired) as $infestedcc) {
				if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries[". $infestedcc->order ."]::when()=". simple_time($infestedcc->when()) .", has ". $infestedcc->larvae ." larvae.");
				$time = min($time, $infestedcc->when());
			}
		} else {
			$infestedccs = clone $this;
			for($i = 0; $i < $larvae; $i++) {
				$time = $infestedccs->when(1, $tagsRequired);
				$infestedccs->expend($time, 1, $tagsRequired);
			}
		}
		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::when(), returns ". $time);
		return $time;
	}

	/**
	 * Calculate time when another vomit can be queued on any infestedcc.
	 * @return float
	 */
	public function whenVomit() {
		$time = INF;
		foreach($this->_infestedccs as $infestedcc) {
			$time = min($time, $infestedcc->whenVomit());
		}
		if(self::$debug) tracemsg(($this->_isClone ? "lon " : "") ."Hatcheries::whenVomit(), returns ". simple_time($time));
		return $time;
	}
};
?>