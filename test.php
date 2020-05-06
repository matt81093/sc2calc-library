<?php

require_once __DIR__.'/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$order = <<<BUILD
9 Pylon
10 Probe*
12 Gateway
12 Probe*
14 Assimilator, then put 3 probes on gas (2 seconds)
15 Probe*
16 Pylon
18 Cybernetics Core
18 Assimilator, then put 3 probes on gas (2 seconds)
18 Probe*
21 Stalker
23 Probe*
24 Stargate
24 Pylon
25 Warpgate
26 Stalker
29 Void Ray**
BUILD;

$order = <<<BUILD
10 Supply Depot
12 Barracks
14 Refinery, then put 3 SCVs on gas (2 seconds)
16 Orbital Command, then constant Calldown: MULE
17 Barracks, then Tech Lab on Barracks, then Stimpack
18 Barracks, then Reactor on Barracks
BUILD;


\holonet\sc2calc\Sc2Calc::$DEBUG_PRINT = true;
$sc2calc = new \holonet\sc2calc\Sc2Calc();
$sc2calc->fromBuildOrderString($order);
