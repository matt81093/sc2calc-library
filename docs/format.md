## Example

`10 Supply Depot`

Build a Supply Depot.

`12 Barracks`

Build a Barracks.

`13 Refinery, then put 3 SCVs on gas (2 seconds)`

Build a Refinery.  
When it completes, put 3 SCVs on it.  
Assume the SCVs lose 2 seconds of mining time.

`15 Orbital Command, then constant Calldown: MULE`

Morph the Command Center into an Orbital Command.  
When it completes, start constant MULE production.

`15 Refinery, then put 3 SCVs on gas (2 seconds)`

Build a Refinery.  
When it completes, put 3 SCVs on it.  
Assume the SCVs lose 2 seconds of mining time.

`15 Marine`

Build a single Marine.

`16 Supply Depot & Marine [2]`

Build another Supply Depot.  
When it started building, build two more Marines.

`19 Factory`

Build a Factory.

`20 Reactor on Barracks`

Build a Reactor on the Barracks.

`21 Starport`

Build a Starport.

`22 Tech Lab on Factory`

Build a Tech Lab on the Factory.

`23 Swap Tech Lab on Factory to Starport`

Lift-off the Factory and Starport, then land them in each others previous position.

## Flow

````
line =
    fixed_job [glue variable_jobs]
    | checkpoint
    | comment
fixed_job =
    (integer | ("@" integer ("minerals" | "gas"))) job
glue =
    "   and" | "&" | "then" | ">"
variable_jobs =
    job [glue variable_jobs]
````

A build order consists of a number of jobs, such as building structures or transferring workers. The first job on a line must have a trigger, some fixed number of supply, minerals or gas, which must be achieved before the job can be started. These _fixed jobs_ will always be performed in the order in which they appear.

`10 Supply Depot`

Build a Supply Depot when you have 10 supply.

`@100 minerals Supply Depot`

Build a Supply Depot when you have 100 minerals.

`@100 gas Supply Depot`

Build a Supply Depot when you have 100 gas.

Other jobs can be written after the first job on a line. They will be performed after the preceeding job has been started or has completed, depending on the operator written between the jobs. We shall call them _variable jobs_.

`12 Barracks & Refineryor` `12 Barracks and Refinery`

1) Build a Barracks.  
2) After the Barracks has started building, build a Refinery.

`12 Barracks > Refinery or` `12 Barracks then Refinery or` `12 Barracks, then Refinery`

1) Build a Barracks.  
2) After the Barracks has completed, build a Refinery.

`12 Barracks & Refinery > put 3 SCVs on gas`

1) Build a Barracks.  
2) After the Barracks has started building, build a Refinery.  
3) After the Refinery has completed, put 3 SCVs on it.  

Unlike fixed jobs, the variable jobs may not be performed in the order in which they appear. The time when they are performed may even be a matter of choice, when there are not enough resources available. As a rule of thumb, _fixed jobs take precedence over variable jobs_ whenever a choice must be made.

## Structures, units & upgrades

````
build_job =
    object_name [chronoboosts] [tag] [send_worker]
chronoboosts =
    { "*" }
tag =
    "#" string
    | "from #" string
````

Building anything is as simple as writing down the exact name of the structure, unit or upgrade you want to build. Please use the complete list of all structures, unit, upgrades and morphs to find the proper names.

`9 Pylon`

Build a Pylon when you have 9 supply.

`12 Gateway`

Build a Gateway when you have 12 supply.

`12 Gateway, then Zealot`

1) Build a Gateway when you have 12 supply.  
2) After the Gateway has completed, build a Zealot.

You can _tag_ any structure or spellcaster with an alphanumeric name, so that you can refer back to it at a later point in the build order. This is useful if you want to build units from one specific structure, or morph a specific structure. If not specified, it is up to the reader of the build order to choose which production queue or spellcaster to use.

N.B. If you put a unit, upgrade, or morph on the same line after a structure from which it can be built, that structure will always be used.

`12 Gateway #1  
...  
18 Zealot from #1`

1) Build a Gateway.  
2) At a later point, build a Zealot from that Gateway.

`12 Gateway #bob  
...  
18 Zealot from #bob`

1) Build a Gateway.  
2) At a later point, build a Zealot from that Gateway.

`12 Gateway, then Zealot`

1) Build a Gateway.  
2) After the Gateway has completed, build a Zealot from that Gateway.

## Add-ons

The syntax for building add-ons and swapping them between structures is listed in the complete product list under Terran Morphs.

`14 Tech Lab on Barracks`

Build a Tech Lab on a Barracks.

`12 Barracks, then Tech Lab on Barracks`

1) Build a Barracks.  
2) After the Barracks has completed, build a Tech Lab on it.

`20 Swap Tech Lab on Barracks to Factory`

Swap the Tech Lab from a Barracks to a Factory.

`22 Swap Reactor on Barracks with Tech Lab on Factory`

Swap the Reactor from a Barracks with the Tech Lab from a Factory.

`12 Barracks #1, then Reactor on Barracks #1  
15 constant Marine from #1`

1) Build a Barracks.  
2) After the Barracks has completed, build a Reactor on it.  
3) When you reach 15 supply, use that Barracks constantly build Marines.

`12 Barracks, then Reactor on Barracks, then constant Marine`

1) Build a Barracks.  
2) After the Barracks has completed, build a Reactor on it.  
3) After the Reactor has completed, constantly build Marines from that Barracks.

## Morphing

The syntax for unit and structure morphs is listed in [this list](list.php) under Protoss, Terran & Zerg Morphs.

`15 Orbital Command`

Morph a Command Center into an Orbital Command.

`20 Gateway, then Transform to Warpgate`

1) Build a Gateway.  
2) After the Gateway has completed, morph it to a Warpgate.

## Transferring workers

````
transfer_job =
    ("transfer" | "+") integer [worker_name] [transfer_time]
    | ("put" | "+") integer [worker_name] "on" ("gas" | "minerals") [transfer_time]
    | ("take" | "-") integer [worker_name] "off" ("gas" | "minerals") [transfer_time]
worker_name =
    "drones" | "probes" | "SCVs" | "workers"
````

`14 Assimilator > +3or  
14 Assimilator, then transfer 3or  
14 Assimilator, then transfer 3 probes`

1) Build an Assimilator.  
2) After the Assimilator has completed, put 3 probes on it.

`21 Nexus > +8or  
21 Nexus, then transfer 8or  
21 Nexus, then transfer 8 probes`

1) Build a Nexus.  
2) After the Nexus has completed, transfer 8 probes to it.

`@100 gas -3 off gasor  
@100 gas take 3 off gasor  
@100 gas take 3 probes off gas`

After 100 gas has been mined, take 3 probes off gas.

`18 Queen > +3 on gasor  
18 Queen, then put 3 on gasor  
18 Queen, then put 3 drones on gas`

1) Build a Queen.  
2) After the Queen has completed, put 3 drones on (back) on gas.

## Repeating

````
job =
    single_job
    | single_job "[" (integer | "auto") "]"
    | "constant" single_job
    | "cancel" object_name
single_job =
    build_job| scout_job | transfer_job | trick_job | fake_job | kill_job
````

`10 Gateway [2]`

Build 2 Gateways when you have 10 supply.

`7 Zergling [3]`

Build 3 pairs of Zergling, one at 7 supply, another at 8 supply and another at 9 supply.

`12 Gateway, then Zealot [auto]or  
12 Gateway, then constant Zealot`

1) Build a Gateway when you have 12 supply.  
2) After the Gateway has completed, constantly build Zealots from that Gateway.

`12 Gateway, then constant Zealot  
...  
22 Cancel Zealot`

1) Build a Gateway when you have 12 supply.  
2) After the Gateway has completed, constantly build Zealots from that Gateway.  
3) When you have 22 supply, stop constant Zealot production.

N.B. _Constant jobs_ will be performed whenever possible without delaying any fixed or variable job. If there are multiple constant jobs (e.g. constant SCV production and constant Marine production), the choice as to which job to prioritize is up to the reader.

## Scouting

````
scout_job =
    "scout"
````

`10 Supply Depot, then Scout`

1) Build a Supply Depot.  
2) After the Supply Depot has completed, send an SCV to scout.

`10 Supply Depot, then Scout (30 seconds)`

1) Build a Supply Depot.  
2) After the Supply Depot has completed, send an SCV to scout.  
(The scout will arrive at the location to be scouted after 30 seconds.)

`12 Proxy Barracks`

Build a Barracks using an SCV previously sent to scout.

`10 Supply Depot, then Scout (30 seconds)  
12 Proxy Barracks`

1) Build a Supply Depot.  
2) After the Supply Depot has completed, send an SCV to scout, arriving after 30 seconds.  
3) Build a Barracks using the SCV previously sent to scout.

## Abilities

`16 Queen, then constant Spawn Larvae`

1) Build a Queen.  
2) After the Queen has completed, constantly use Spawn Larvae ability.

`15 Orbital Command, then constant Calldown: MULE`

1) Morph a Command Center into an Orbital Command.  
2) After the Orbital Command has completed, constantly use Calldown: MULE ability.

`15 Zealot*`

1) Build a Zealot.  
2) Use Chronoboost ability on the Gateway building the Zealot.

`17 Cybernetics Core, then Warpgate***`

1) Build a Cybernetics Core.  
2) Build the Warpgate upgrade at that Cybernetics Core.  
3) Use Chronoboost ability on the Cybernetics Core thrice.

## Miscellaneous

````
checkpoint =
    integer ":" integer "checkpoint"
trick_job =
    ["double"] string "trick" ["into" string ["[" integer "]"]]
fake_job =
    "fake" string
kill_job =
    "kill" string
comment =
    "#" string
````

`5:00 checkpoint`

In the results, the resources and energy surplus at 5 minutes will be shown.

`14 Fake Hatchery`

1) Build a Hatchery.  
2) Cancel the Hatchery

`22 Kill Zealot [2]`

Lose two Zealots when you have 22 supply

`10 Extractor Trick`

1) Build an Extractor.  
2) After the Extractor has started building, build a Drone.  
3) Cancel the Extractor.

`10 Double Extractor Trick`

1) Build two Extractors.  
2) After the Extractors have started building, build two Drones.  
3) Cancel the Extractors.

`10 Double Extractor Trick into Drone`

1) Build two Extractors.  
2) After the Extractors have started building, build _one_ Drone.  
3) Cancel the Extractors.

`10 Double Extractor Trick into Zergling [2]`

1) Build two Extractors.  
2) After the Extractors have started building, build two pairs of Zergling.  
3) Cancel the Extractors.

`# build this barracks outside the opponent's natural`

Comments are ignored by the calculator.

## Realism

````
transfer_time =
    "(" integer ("s" | "sec" | "seconds") ["lost"] ")"
send_worker =
    "(send @" integer ("gas" | "minerals") ")"
````

`# Startup build delay = 3 seconds`

Nothing is built in the first three seconds.

`# Startup mining delay = 2 seconds`

No minerals are mined in the first two seconds.

`12 Gateway (send @120 minerals)`

1) Send probe when you have 120 minerals.  
2) Build Gateway when 150 minerals become available.

`22 Nexus > +8 (10s)or  
22 Nexus, then transfer 8 probes (10 seconds lost)`

1) Build a Nexus.  
2) After Nexus has completed, transfer 8 probes to the new base.  
(these workers aren't mining for 10 seconds)
