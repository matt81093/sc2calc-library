<?php
/**
 * This file is part of the library rewrite based on sc2calc.org
 * (c) Matthias Lantsch.
 *
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2calc\format\parser;

use holonet\sc2calc\job\Job;
use holonet\sc2calc\enum\Race;
use holonet\sc2calc\job\KillJob;
use holonet\sc2calc\job\BuildJob;
use holonet\sc2calc\job\ScoutJob;
use holonet\sc2calc\job\TrickJob;
use holonet\sc2calc\job\CancelJob;
use holonet\sc2calc\job\MutateJob;
use holonet\sc2calc\logic\Product;
use holonet\sc2calc\job\Dependency;
use holonet\sc2calc\mutation\Mutation;
use holonet\sc2calc\timeline\Checkpoint;
use holonet\sc2calc\mutation\TransferMutation;
use holonet\sc2calc\error\StringParserException;
use holonet\sc2calc\error\InvalidObjectParserException;

/**
 * Sc2CalcStringParser reads a text-based build order and extracts jobs from it.
 */
class Sc2CalcStringParser extends Parser {
	public const DELAY_REGEX = '(\\(-?(?P<delay>\\d+)\\s*s(ec(onds)?)?(\\s+lost)?\\))';

	public const INITIATE_REGEX = '(\\(send\\s+'.self::WORKER_REGEX.'?\\s*(@|at\\s+)(?P<initiate_amount>\\d+)\\s+(?P<initiate_resource>gas|minerals?)\\))';

	public const WORKER_REGEX = "((worker|probe|drone|SCV|infestedSCV'?)s?)";

	/**
	 * Parse a single line of a build order.
	 * @param int $lineNumber
	 * @param string $line
	 */
	public function read($lineNumber, $line): void {
		$ScoutingWorker = $this->productManager->byIdentifier('ScoutingWorker');
		$Drone = $this->productManager->byIdentifier('Drone');

		$line = str_replace(',', '', $line);

		$jobStack = array();

		// split line into commands
		$commands = preg_split('/(&|>|\\s+and\\s+|\\s+then\\s+)/', $line, null, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0; $i < count($commands); $i += 2) {
			$command = trim($commands[$i]);
			$operator = $i === 0 ? null : trim($commands[$i - 1]);

			// wipe slate
			unset($job, $product, $triggerGas, $triggerMineral, $triggerSupply);

			// extract trigger from command
			if (empty($operator)) {

				// trigger is a supply number
				if (preg_match('/^(?P<supply>\\d+)\\s+(?P<command>.+)$/', $command, $terms)) {
					$triggerSupply = (int)$terms['supply'];
					$command = trim($terms['command']);

				// trigger is a resource number
				} elseif (preg_match('/^@(?P<amount>\\d+)\\s+(?P<resource>minerals?|gas)\\s+(?P<command>.+)$/', $command, $terms)) {
					if (strcasecmp($terms['resource'], 'gas') === 0) {
						$triggerGas = $terms['amount'];
					} else {
						$triggerMineral = $terms['amount'];
					}
					$command = trim($terms['command']);
				}
			}

			// repeating or recurring
			unset($repeat);
			$recurring = false;
			if (preg_match('/^\\s*(?P<command>.*)\\s*\\[(?P<repeat>\\d+)\\]\\s*$/', $command, $terms)) {
				$repeat = (int)$terms['repeat'];
				$command = trim($terms['command']);
			} elseif (preg_match('/^\\s*(?P<command>.*)\\s*\\[(?P<repeat>auto)\\]\\s*$/i', $command, $terms)) {
				$recurring = true;
				$command = trim($terms['command']);
			} elseif (preg_match('/^\\s*(?P<repeat>constant)\\s+(?P<command>.*)\\s*$/i', $command, $terms)) {
				$recurring = true;
				$command = trim($terms['command']);
			}

			// required tags
			if (preg_match('/^\\s*(?P<command>.*?)\\s+from\\s+(?P<tags>#[a-zA-Z0-9]+(\\s*(\\s+and\\s+)?\\s*#[a-zA-Z0-9]+)*)\\s*$/i', $command, $terms)) {
				if (preg_match_all('/#(?<tags>[a-zA-Z0-9]+)/', $terms['tags'], $tags)) {
					$tagsRequired = $tags['tags'];
					$command = trim($terms['command']);
				} else {
					unset($tagsRequired);
				}
			} else {
				unset($tagsRequired);
			}

			// tag
			if (preg_match('/^\\s*(?P<command>.*?)\\s*#(?P<tag>[a-zA-Z0-9]+)\\s*$/', $command, $terms)) {
				$tag = $terms['tag'];
				$command = trim($terms['command']);
			} else {
				unset($tag);
			}

			// chrono boost
			if (preg_match('/^\\s*(?P<command>.*?)\\s*(?P<chronoboost>\\*+)\\s*$/', $command, $terms)) {
				$chronoboost = mb_strlen($terms['chronoboost']);
				$command = trim($terms['command']);
			} else {
				$chronoboost = 0;
			}

			// skip empty command
			if ($command === '') {
				continue;
			}

			// scout
			if (preg_match('/^\\s*scout\\s*'.static::DELAY_REGEX.'?/i', $command, $terms)) {
				if (!empty($terms['delay'])) {
					$delay = (int)$terms['delay'];
				} else {
					$delay = 0;
				}
				$job = new ScoutJob($ScoutingWorker, $delay);

			// option
			} elseif (preg_match('/^\\s*#(?P<name>[\\w\\s]+)=(?P<value>[a-zA-Z0-9\\s]+)$/i', $command, $terms)) {
				$name = mb_strtolower(trim($terms['name']));
				if (in_array($name, array('startup build delay', 'startup mining delay'))) {
					if (preg_match('/(?P<delay>\\d+)\\s*s(ec(onds)?)?/i', trim($terms['value']), $terms)) {
						$this->options[$name] = (int)$terms['delay'];
					}
				} else {
					$this->options[$name] = trim($terms['value']);
				}

				// comment
			} elseif (preg_match('/^\\s*#.*$/i', $command, $terms)) {

				// transfer workers to product constructed in previous job
			} elseif (preg_match('/^\\s*(transfer\\s+|\\s*[+])(?P<workers>\\d+)\\s*'.static::WORKER_REGEX.'?\\s*'.static::DELAY_REGEX.'?$/i', $command, $terms)) {

				// transfer target
				unset($transferTarget);
				if (count($jobStack) !== 0 && end($jobStack)->productBuilt() !== null) {
					$transferTarget = end($jobStack)->productBuilt();
				}

				if (isset($transferTarget) && count($jobStack) > 0 && in_array('Base', $transferTarget->types)) {
					$mutation = new TransferMutation((int)$terms['workers']);
				} elseif (isset($transferTarget) && count($jobStack) > 0 && in_array('Geyser', $transferTarget->types)) {
					$mutation = new Mutation((int)-$terms['workers'], (int)$terms['workers']);
				} else {
					throw new StringParserException(
						($lineNumber + 1),
						'Transfer workers where?',
						"You can only use the syntax 'transfer 3 workers' or '+3' directly after a job that builds a base or a geyser. In other cases, please write something like 'put 3 workers on gas' or '+3 on gas'."
					);
				}
				if (isset($terms['delay'])) {
					$mutation->delay = (int)$terms['delay'];
				}
				$job = new MutateJob($mutation);

			// transfer workers off one resource to another
			} elseif (preg_match('/^\\s*(?P<verb>put|take|-|[+])\\s*(?P<workers>\\d+)(\\s+'.static::WORKER_REGEX.')?\\s+(?P<preposition>on|off)\\s+(?P<resource>gas|minerals?)\\s*'.static::DELAY_REGEX.'?/i', $command, $terms)) {
				$positive = (strcasecmp($terms['verb'], 'put') === 0 || $terms['verb'] === '+');
				if ($positive xor strcasecmp($terms['preposition'], 'on') === 0) {
					throw new StringParserException(($lineNumber + 1), "You can't '".($positive ? 'put' : 'take')."' miners '{$terms['preposition']}' a resource.");
				}
				if ($positive xor strcasecmp($terms['resource'], 'gas') === 0) {
					$mutation = new Mutation((int)$terms['workers'], (int)-$terms['workers']);
				} else {
					$mutation = new Mutation((int)-$terms['workers'], (int)$terms['workers']);
				}
				if (isset($terms['delay'])) {
					$mutation->delay = (int)$terms['delay'];
				}
				$job = new MutateJob($mutation);

			// checkpoint
			} elseif (preg_match('/^\\s*(?P<minutes>[0-9]{1,2}):(?<seconds>[0-9]{2})\\s+checkpoint\\s*$/i', $command, $terms)) {
				$this->checkpoints[] = (int)$terms['minutes'] * 60 + (int)$terms['seconds'];
			//tracemsg("Checkpoint at ". ((int)$terms["minutes"] * 60 + (int)$terms["seconds"]) ." seconds");

				// cancel
			} elseif (preg_match('/^Cancel '.static::RegexProduct().'\\s*$/i', $command, $terms)) {

				// create job
				$product = $this->productManager->byName(trim($terms['product']));
				if (empty($product)) {
					throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['product']);
				}
				$job = new CancelJob($product);

			// trick
			} elseif (preg_match('/^(?P<plural>double\\s+)?'.static::RegexProduct('pledge_product').'\\s+trick(\\s+into\\s+'.static::RegexProduct('turn_product').')?\\s*$/i', $command, $terms)) {

				// parse pledge
				$pledgeProduct = $this->productManager->byName($terms['pledge_product']);
				if (empty($pledgeProduct)) {
					throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['pledge_product']);
				}
				$pledgeCount = empty($terms['plural']) ? 1 : 2;

				// parse turn
				if (empty($terms['turn_product'])) {
					$turnProduct = $Drone;
					$turnCount = $pledgeCount;
				} else {
					$turnProduct = $this->productManager->byName($terms['turn_product']);
					if (empty($turnProduct)) {
						throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['turn_product']);
					}
					$turnCount = $repeat ?? 1;
				}
				unset($repeat);

				// create job
				$job = new TrickJob($pledgeProduct, $pledgeCount, $turnProduct, $turnCount);

			// fake
			} elseif (preg_match('/^\\s*fake '.static::RegexProduct('pledge_product').'\\s*$/i', $command, $terms)) {

				// parse pledge
				$pledgeProduct = $this->productManager->byName($terms['pledge_product']);
				if (empty($pledgeProduct)) {
					throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['pledge_product']);
				}
				$pledgeCount = 1;

				// parse turn
				$turnProduct = null;
				$turnCount = 0;

				// create job
				$job = new TrickJob($pledgeProduct, $pledgeCount, $turnProduct, $turnCount);

			// kill
			} elseif (preg_match('/^\\s*kill '.static::RegexProduct('product').'\\s*$/i', $command, $terms)) {

				// parse product
				$product = $this->productManager->byName($terms['product']);
				if (empty($product)) {
					throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['product']);
				}
				if (in_array('Structure', $product->types)) {
					throw new StringParserException(($lineNumber + 1), "Cannot kill structure '{$terms['product']}'");
				}

				// create job
				$job = new KillJob($product);

			// build
			} elseif (preg_match('/^(?P<proxy>proxy\\s+)?'.static::RegexProduct().'(?P<priority>\\s*!)?\\s*'.static::INITIATE_REGEX.'?$/i', $command, $terms)) {
				// create job
				$product = $this->productManager->byName(trim($terms['product']));
				if (empty($product)) {
					throw new InvalidObjectParserException(($lineNumber + 1), 'product', $terms['product']);
				}
				$job = new BuildJob($product);

				// initiate at given resource amount
				if (isset($terms['initiate_amount'], $terms['initiate_resource'])) {
					if (strcasecmp($terms['initiate_resource'], 'gas') === 0) {
						$job->initiateGas = (int)$terms['initiate_amount'];
					} else {
						$job->initiateMineral = (int)$terms['initiate_amount'];
					}
				}

				// proxy
				if (!empty($terms['proxy'])) {
					$job->queueTypeExpended = $ScoutingWorker;
				}

				// priority job
				//@todo do we need this (what was this used for?)
//				if (isset($terms['priority'])) {
//					$job->superPriority = true;
//				}

				// unknown command
			} else {
				throw new InvalidObjectParserException(($lineNumber + 1), 'command', $command);
			}

			if (isset($job)) {

				// trigger is the previous job
				if (!isset($triggerGas) && !isset($triggerMineral) && !isset($triggerSupply)) {
					if (count($jobStack) === 0) {
						throw new StringParserException(($lineNumber + 1), 'There is no trigger to this job.',
							"The trigger for a job can either by a supply count (for example '12 Gateway') or an amount of resources (for example '@100 gas take 3 off gas'). A job that appears at the start of a line must have one of these triggers."
						);
					}
					$dependency = new Dependency(end($jobStack), ($operator !== null && ($operator === '>' || strcasecmp($operator, 'then') === 0)) ? Dependency::AT_COMPLETION : Dependency::AT_START);
				}

				// set triggers
				if (isset($dependency)) {
					$job->dependency = $dependency;
				}
				if (isset($triggerGas)) {
					$job->triggerGas = $triggerGas;
				}
				if (isset($triggerMineral)) {
					$job->triggerMineral = $triggerMineral;
				}
				if (isset($triggerSupply)) {
					$job->triggerSupply = $triggerSupply;
				}

				// if job is recurring and its production queue appears in the
				// stack, tag the queue and have job require that tag
				if (!isset($tagsRequired) && isset($product, $product->expends)
					&& ($recurring)) {
					unset($queueJob);
					for ($j = count($jobStack) - 1; $j >= 0 && !isset($queueJob); $j--) {
						if ($jobStack[$j]->productBuilt() !== null) {
							foreach ($product->expends as $expended) {
								if ($expended->uid === $jobStack[$j]->productBuilt()->uid) {
									$queueJob = $jobStack[$j];

									break;
								}
							}
						}
					}
					if (isset($queueJob)) {
						if (!isset($queueJob->tag)) {
							$queueJob->tag = uniqid();
						}
						$tagsRequired = array($queueJob->tag);
					}
				}

				// tag
				if (isset($tag)) {
					$job->tag = $tag;
				}

				// require tags
				if (isset($tagsRequired)) {
					$job->tagsRequired = $tagsRequired;
				}

				// chrono boost
				if ($chronoboost) {
					if (!isset($product)) {
						throw new StringParserException($lineNumber + 1, "Could not chronoboost '{$command}': Unknown product");
					}

					if (!$product->race->equals(Race::PROTOSS())) {
						throw new StringParserException($lineNumber + 1, "Could not chronoboost '{$command}': Cannot chronoboost non-protoss units/structures");
					}

					if (in_array('Structure', $product->types)) {
						throw new StringParserException($lineNumber + 1, "Could not chronoboost '{$command}': Cannot chronoboost the construction of structures");
					}

					$job->chronoboost = $chronoboost;
				}

				// add job(s)
				$this->jobs[] = $job;
				$jobStack[] = $job;

				// repeat job
				for ($j = 1; $j < ($repeat ?? 1); $j++) {
					$job = clone $job;
					$job->dependency = new Dependency(end($jobStack), Dependency::AT_START);
					if (isset($job->triggerSupply)) {
						$job->triggerSupply += $job->supplyCost();
					}
					if (isset($product) && (in_array('Morph', $product->types))) {
						$job->tagsRequired = null;
					}
					$job->triggerGas = null;
					$job->triggerMineral = null;
					$this->jobs[] = $job;
					$jobStack[] = $job;
				}

				// recur job after the first go
				if ($recurring) {
					$job = clone $job;
					$job->dependency = new Dependency(end($jobStack), Dependency::AT_START);
					$job->triggerGas = null;
					$job->triggerMineral = null;
					$job->triggerSupply = null;
					$job->recurring = $recurring;
					$this->jobs[] = $job;
				}
			}
		}
	}

	protected function parseBody(string $build): void {
		// parse line-by-line
		$lines = preg_split("/([\n])/", $build);
		foreach ($lines as $lineNumber => $line) {
			$this->read($lineNumber, trim($line));
		}
	}

	private static function RegexProduct(string $tag = 'product'): string {
		return '(?P<'.$tag.'>[0-9a-zA-Z :-]+)';
	}
}
