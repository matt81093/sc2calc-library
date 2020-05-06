<?php

	$ProtossStructures = [
		'$Nexus' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Nexus",
			'$type' => Structure | Base | Booster | Spellcaster | Farm,
			'$materials' => [
				'$minerals' => 400, 
				'$gas' => 0],
			'$build_time' => 100,
			'$prerequisites' => [],
			'$supplyCapacity' => 10,
			'$energyStart' => 0,
			'$energyMax' => 100,
		],
		
		'$Pylon' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Pylon",
			'$type' => Structure | Farm,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 0], 
			'$build_time' => 25,
			'$prerequisites' => [],
			'$supplyCapacity' => 8,
		],

		'$Assimilator' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Pylon",
			'$type' => Structure | Geyser,
			'$materials' => [
				'$minerals' => 75, 
				'$gas' => 0],
			'$build_time' => 30,
			'$prerequisites' => [],
		],
		
		'$Gateway' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Pylon",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0],
			'$build_time' => 65,
			'$prerequisites' => ['$Nexus', '$Pylon'],
		],
		
		'$Warpgate' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Pylon",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$build_time' => 30,
			'$prerequisites' => [],
		],
		
		'$Forge' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Forge",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0],
			'$build_time' => 45,
			'$prerequisites' => ['$Nexus', '$Pylon'],
		],
		
		'$PhotonCannon' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Photon Cannon",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0], 
			'$build_time' => 40,
			'$prerequisites' => ['$Forge', '$Pylon'],
		],
		
		'$CyberneticsCore' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Cybernetics Core",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0], 
			'$build_time' => 50,
			'$prerequisites' => ['$Gateway', '$Pylon'],
		],
		
		'$TwilightCouncil' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Twilight Council",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 100], 
			'$build_time' => 50,
			'$prerequisites' => [],
		],
		
		'$RoboticsFacility' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Robotics Facility",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 200, 
				'$gas' => 100], 
			'$build_time' => 65,
			'$prerequisites' => ['$CyberneticsCore', '$Pylon'],
		],
		
		'$Stargate' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Stargate",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 150], 
			'$build_time' => 60,
			'$prerequisites' => ['$CyberneticsCore', '$Pylon'],
		],
		
		'$TemplarArchives' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Templar Archives",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 200], 
			'$build_time' => 50,
			'$prerequisites' => ['$TwilightCouncil', '$Pylon'],
		],
		
		'$DarkShrine' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Dark Shrine",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 200], 
			'$build_time' => 100,
			'$prerequisites' => ['$TwilightCouncil', '$Pylon'],
		],
		
		'$RoboticsBay' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Robotics Bay",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 200, 
				'$gas' => 200],
			'$build_time' => 65,
			'$prerequisites' => ['$RoboticsFacility', '$Pylon'],
		],
		
		'$FleetBeacon' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Fleet Beacon",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 300, 
				'$gas' => 200], 
			'$build_time' => 60,
			'$prerequisites' => ['$Stargate', '$Pylon'],
		],
	];
	
	// Protoss Upgrades 
	
	$ProtossUpgrades = [
		'$WarpgateUpgrade' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Warpgate",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$build_time' => 160,
		],
		
		'$GroundWeaponslevel1' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Weapons Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$GroundWeaponslevel2' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Weapons Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 190,
		],
		
		'$GroundWeaponslevel3' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Weapons Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 220,
		],
		
		'$GroundArmorlevel1' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Armor Level 1",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$GroundArmorlevel2' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Armor Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 190,
		],
		
		'$GroundArmorlevel3' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Ground Armor Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 220,
		],
		
		'$AirWeaponslevel1' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Weapons Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$AirWeaponslevel2' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Weapons Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$build_time' => 190,
		],
		
		'$AirWeaponslevel3' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Weapons Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$build_time' => 220,
		],
		
		'$AirArmorlevel1' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Armor Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 160,
		],
		
		'$AirArmorlevel2' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Armor Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 225,
				'$gas' => 225],
			'$build_time' => 190,
		],
		
		'$AirArmorlevel3' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Air Armor Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 300],
			'$build_time' => 220,
		],
		
		'$Shieldslevel1' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Shields Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 160,
		],
		
		'$Shieldslevel2' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Shields Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 225,
				'$gas' => 225],
			'$build_time' => 190,
		],
		
		'$Shieldslevel3' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Shields Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$TwilightCouncil'],
			'$expends' => ['$Forge'],
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 300],
			'$build_time' => 220,
		],
		
		'$Charge' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Charge",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TwilightCouncil'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 140,
		],
		
		'$GraviticBoosters' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Gravitic Boosters",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 80,
		],
		
		'$GraviticDrive' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Gravitic Drive",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 80,
		],
		
		'$FluxVanes' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Flux Vanes",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$FleetBeacon'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 80,
		],
			
		'$GraviticDrive' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Gravitic Drive",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 80,
		],
		
		'$ExtendedThermalLance' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Extended Thermal Lance",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsBay'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 140,
		],
		
		'$PsionicStorm' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Psionic Storm",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TemplarArchives'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 110,
		],
		
		'$Hallucination' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Hallucination",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$CyberneticsCore'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 110,
		],
		
		'$Blink' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Blink",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TwilightCouncil'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$KhaydarinAmulet' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Khaydarin Amulet",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TemplarArchives'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$GravitonCatapult' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Graviton Catapult",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$FleetBeacon'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 80,
		],
	];
	// Protoss Morphs
	
	$ProtossMorphs = [
		'$TransformToWarpgate' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Transform to Warpgate",
			'$type' => Morph,
			'$prerequisites' => ['$WarpgateUpgrade'],
			'$expends' => ['$Gateway'],
			'$yeilds' => ['$Warpgate'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 0,
				'$gas' => 0,],
			'$build_time' => 10,
		],
		
		'$TransformToGateway' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Transform to Gateway",
			'$type' => Morph,
			'$prerequisites' => ['$WarpgateUpgrade'],
			'$expends' => ['$Warpgate'],
			'$yeilds' => ['$Gateway'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 0,
				'$gas' => 0,],
			'$build_time' => 10,
		],
	];
	
	// Protoss Units
	
	$ProtossUnits = [
		'$Probe' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Probe",
			'$type' => Unit | Worker,
			'$prerequisites' => [],
			'$expends' => ['$Nexus'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 0],
			'$build_time' => 17,
		],
		
		'$Zealot' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Zealot",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Gateway', '$Warpgate'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 0],
			'$build_time' => 38,
		],
		
		'$Stalker' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Stalker",
			'$type' => Unit,
			'$prerequisites' => ['$CyberneticsCore'],
			'$expends' => ['$Gateway', '$Warpgate'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 125,
				'$gas' => 50],
			'$build_time' => 42,
		],
		
		'$HighTemplar' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "High Templar",
			'$type' => Unit,
			'$prerequisites' => ['$TemplarArchives'],
			'$expends' => ['$Gateway', '$Warpgate'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' =>50,
				'$gas' => 150],
			'$build_time' => 55,
		],
		
		'$DarkTemplar' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Dark Templar",
			'$type' => Unit,
			'$prerequisites' => ['$DarkShrine'],
			'$expends' => ['$Gateway', '$Warpgate'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 125,
				'$gas' => 125],
			'$build_time' => 55,
		],
		
		'$Observer' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Observer",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsFacility'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 25,
				'$gas' => 75],
			'$build_time' => 40,
		],
		
		'$Immortal' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Immortal",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsFacility'],
			'$supplyCost' => 4,
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 100],
			'$build_time' => 50,
		],
		
		'$WarpPrism' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Warp Prism",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$RoboticsFacility'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 0],
			'$build_time' => 50,
		],
		
		'$Colossus' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Colossus",
			'$type' => Unit,
			'$prerequisites' => ['$RoboticsBay'],
			'$expends' => ['$RoboticsFacility'],
			'$supplyCost' => 6,
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 200],
			'$build_time' => 75,
		],
		
		'$Phoenix' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Phoenix",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Stargate'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 100],
			'$build_time' => 35,
		],
		
		'$VoidRay' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Void Ray",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Stargate'],
			'$supplyCost' => 3,
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 150],
			'$build_time' => 60,
		],
		
		'$Carrier' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Carrier",
			'$type' => Unit,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$Stargate'],
			'$supplyCost' => 6,
			'$materials' => [
				'$minerals' => 350,
				'$gas' => 250],
			'$build_time' => 120,
		],
		
		'$Interceptor' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Interceptor",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Carrier'],
			'$supplyCost' => 0,
			'$materials' => [
				'$minerals' => 25,
				'$gas' => 0],
			'$build_time' => 8,
		],
		
		'$Mothership' => [
			'$race' => Protoss,
			'$commander' => null,
			'$name' => "Mothership",
			'$type' => Unit,
			'$prerequisites' => ['$FleetBeacon'],
			'$expends' => ['$Nexus'],
			'$supplyCost' => 8,
			'$materials' => [
				'$minerals' => 400,
				'$gas' => 400],
			'$build_time' => 160,
		],
	];
	
	$ProtossAbilities = [
		'$ChronoBoost' => [
			'$name' => "Chrono Boost",
			'$race' => Protoss,
			'$commander' => null,
			'$type' => Ability,
			'$prerequisites' => null,
			'$spellCaster' => '$Nexus',
			'$energyCost' => 25,
			'$spellCooldown' => 20,
			'$exposed' => false,
		],
	];
	
	$validProtoss = array_merge($ProtossStructures, $ProtossUnits, $ProtossUpgrades, $ProtossAbilities, $ProtossMorphs);

?>