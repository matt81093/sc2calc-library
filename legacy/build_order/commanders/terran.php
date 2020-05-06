<?php

	require('./helper.php');

	// Terran Structures
	
	$TerranStructures = [
		'CommandCenter' => '{
			"race": "Terran",
			"name": "Command Center",
			"type": "Structure | Base | Farm",
			"mineralCost": 400,
			"gasCost": 0,
			"timeCost": 100,
			"prerequisites": null,
			"supplyCapacity": 11
		}',
	];
	
	$TerranUnits = [
		'SCV' => '{
			"race": "Terran",
			"name": "SCV",
			"type": "Unit | Worker",
			"prerequisites": null,
			"expends": "$CommandCenter | $OrbitalCommand | $PlanetaryFortress",
			"supplyCost": 1,
			"mineralCost": 50,
			"gasCost": 0,
			"timeCost": 17
		}'
	];
	
	/*
	$TerranStructures = [
		'$OrbitalCommand' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Orbital Command",
			'$type' => Structure | Base | Spellcaster | Farm,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => [],
			'$supplyCapacity' => 11,
			'$energyStart' => 50,
			'$energyMax' => 100,
		],
		
		'$PlanetaryFortress' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Planetary Fortress",
			'$type' => Structure | Base | Farm,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => [],
			'$supplyCapacity' => 11,
		],
		
		'$SupplyDepot' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Supply Depot",
			'$type' => Structure | Farm,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 0],
			'$timeCost' => 30,
			'$prerequisites' => ['$CommandCenter'],
			'$supplyCapacity' => 8,
		],
		
		'$Refinery' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Refinery",
			'$type' => Structure | Geyser,
			'$materials' => [
				'$minerals' => 75, 
				'$gas' => 0],
			'$timeCost' => 30,
			'$prerequisites' => ['$CommandCenter'],
		],
		
		'$Barracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Barracks",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0],
			'$timeCost' => 65,
			'$prerequisites' => ['$CommandCenter', '$SupplyDepot'],
		],
		
		'$EngineeringBay' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Engineering Bay",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 100],
			'$timeCost' => 60,
			'$prerequisites' => [],
		],
		
		'$Bunker' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Bunker",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 0],
			'$timeCost' => 40,
			'$prerequisites' => ['$Barracks'],
		],
		
		'$MissileTurret' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Missile Turret",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 0],
			'$timeCost' => 25,
			'$prerequisites' => ['$EngineeringBay'],
		],
		
		'$SensorTower' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Sensor Tower",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 125, 
				'$gas' => 100],
			'$timeCost' => 25,
			'$prerequisites' => ['$EngineeringBay'],
		],
		
		'$Factory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Factory",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 100],
			'$timeCost' => 60,
			'$prerequisites' => ['$Barracks'],
		],
		
		'$GhostAcademy' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ghost Academy",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 50],
			'$timeCost' => 40,
			'$prerequisites' => ['$Barracks'],
		],
		
		'$Armory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Sensor Tower",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 100],
			'$timeCost' => 65,
			'$prerequisites' => ['$Factory'],
		],
		
		'$Starport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Starport",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 100],
			'$timeCost' => 50,
			'$prerequisites' => ['$Factory'],
		],
		
		'$FusionCore' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Fusion Core",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 150],
			'$timeCost' => 50,
			'$prerequisites' => ['$Starport'],
		],
		
		// Terran addons
				
		'$BarracksOnReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Barracks with attached Reactor",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$BarracksOnTechLab' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Barracks with attached Tech Lab",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$FactoryOnReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Factory with attached Reactor",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$FactoryOnTechLab' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Factory with attached Tech Lab",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$StarportOnReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Starport with attached Reactor",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$StarportOnTechLab' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Starport with attached Tech Lab",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$Reactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$ReactorOnBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor attached to Barracks",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$ReactorOnFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor attached to Factory",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$ReactorOnStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor attached to Starport",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$TechLab' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$TechLabOnBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab attached to Barracks",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$TechLabOnFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab attached to Factory",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
		
		'$TechLabOnStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab attached to Starport",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$timeCost' => null,
			'$prerequisites' => null,
		],
	];
	
	// Terran Upgrades
	
	$TerranUpgrades = [
		'$InfantryWeaponsLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Weapons Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$InfantryWeaponsLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Weapons Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Armory'],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$InfantryWeaponsLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Weapons Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Armory'],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$InfantryArmorLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Armor Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$InfantryArmorLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Armor Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Armory'],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$InfantryArmorLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infantry Armor Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Armory'],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$VehicleWeaponsLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Weapons Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$VehicleWeaponsLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Weapons Level 2",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$VehicleWeaponsLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Weapons Level 3",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$VehiclePlatingLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Plating Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$VehiclePlatingLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Plating Level 2",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$VehiclePlatingLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Vehicle Plating Level 3",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$ShipWeaponsLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Weapons Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$ShipWeaponsLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Weapons Level 2",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$ShipWeaponsLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Weapons Level 3",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$ShipPlatingLevel1' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Plating Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 160,
		],
		
		'$ShipPlatingLevel2' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Plating Level 2",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$timeCost' => 190,
		],
		
		'$ShipPlatingLevel3' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ship Plating Level 3",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Armory'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$timeCost' => 220,
		],
		
		'$NitroPacks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Nitro Packs",
			'$type' => Upgrade,
			'$prerequisites' => ['$Factory'],
			'$expends' => ['$TechLabOnBarracks'],
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 100,
		],
		
		'$HiSecAutoTracking' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Hi-Sec Auto Tracking",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 80,
		],
		
		'$PersonalCloaking' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Personal Cloaking",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$GhostAcademy'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 120,
		],
		
		'$CloakingField' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Cloaking Field",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnStarport'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$timeCost' => 110,
		],
		
		'$StrikeCannons' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "250mm Strike Cannons",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnFactory'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 110,
		],
		
		'$SeekerMissile' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Seeker Missile",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnStarport'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 110,
		],
		
		'$WeaponRefit' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Weapon Refit",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$FusionCore'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 60,
		],
		
		'$SiegeTech' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Siege Tech",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnFactory'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 80,
		],
		
		'$Stimpack' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Stimpack",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnBarracks'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 170,
		],
		
		'$ConcussiveShells' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Concussive Shells",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnBarracks'],
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 60,
		],
		
		'$MoebiusReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Moebius Reactor",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnStarport'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 80,
		],
		
		'$CaduceusReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Caduceus Reactor",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnBarracks'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 80,
		],
		
		'$CorvidReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Corvid Reactor",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnStarport'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 110,
		],
		
		'$BehemothReactor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Behemoth Reactor",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$FusionCore'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 80,
		],
		
		'$NeosteelFrame' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Neosteel Frame",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 110,
		],
		
		'$BuildingArmor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Building Armor",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EngineeringBay'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 140,
		],
		
		'$DurableMaterials' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Durable Materials",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnStarport'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 110,
		],
		
		'$CombatShield' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Combat Shield",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnBarracks'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 110,
		],
		
		'$InfernalPreIgniter' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Infernal Pre-Igniter",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$TechLabOnFactory'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 110,
		],
	];
	
	// Terran Morphs
	
	$TerranMorphs = [
		'$SalvageBunker' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Salvage Bunker",
			'$type' => Morph,
			'$prerequisites' => [],
			'$expends' => ['$Bunker'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => -750,
				'$gas' => 0,],
			'$timeCost' => 3,
		],
		
		'$MorphToOrbitalCommand' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Orbital Command",
			'$type' => Morph | Booster,
			'$prerequisites' => ['$Barracks'],
			'$expends' => ['$CommandCenter'],
			'$yeilds' => ['$OrbitalCommand'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 0,],
			'$timeCost' => 35,
		],
		
		'$MorphToPlanetaryFortress' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Planetary Fortress",
			'$type' => Morph,
			'$prerequisites' => ['$EngineeringBay'],
			'$expends' => ['$CommandCenter'],
			'$yeilds' => ['$PlanetaryFortress'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$timeCost' => 50,
		],
		
		'$BuildReactorOnBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$Barracks'],
			'$expends' => ['$BarracksOnReactor', '$ReactorOnBarracks'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 50,
		],
		
		'$BuildTechLabOnBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$Barracks'],
			'$expends' => ['$BarracksOnTechLab', '$TechLabOnBarracks'],
			'$yeilds' => ['$OrbitalCommand'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 25],
			'$timeCost' => 25,
		],
		
		'$BuildReactorOnFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$Factory'],
			'$expends' => ['$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 50,
		],
		
		'$BuildTechLabOnFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$Factory'],
			'$expends' => ['$FactoryOnTechLab', '$TechLabOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 25],
			'$timeCost' => 25,
		],
		
		'$BuildReactorOnStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reactor on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$Starport'],
			'$expends' => ['$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 50,
		],
		
		'$BuildTechLabOnStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Tech Lab on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$Starport'],
			'$expends' => ['$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 25],
			'$timeCost' => 25,
		],
		
		'$SwapReactorOnBarracksToFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Barracks to Factory",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$Factory'],
			'$expends' => ['$Barracks', null, '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnBarracksToStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Barracks to Starport",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$Starport'],
			'$expends' => ['$Barracks', null, '$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnFactoryToBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Factory to Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$Factory'],
			'$expends' => ['$Barracks', null, '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnFactoryToStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Factory to Starport",
			'$type' => Morph,
			'$prerequisites' => ['$FactoryOnReactor', '$ReactorOnFactory', '$Starport'],
			'$expends' => ['$Factory', null, '$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnStarportToBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Starport to Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$Factory'],
			'$expends' => ['$Barracks', null, '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnStarportToFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Starport to Factory",
			'$type' => Morph,
			'$prerequisites' => ['$StarportOnReactor', '$ReactorOnStarport', '$Factory'],
			'$expends' => ['$Starport', null, '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnBarracksToFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Barracks to Factory",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$Factory'],
			'$expends' => ['$Barracks', null, '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnBarracksToStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Barracks to Starport",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$Starport'],
			'$expends' => ['$Barracks', null, '$StarportOnTechLab', '$TechLabOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnFactoryToBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Factory to Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$FactoryOnTechLab', '$TechLabOnFactory', '$Barracks'],
			'$expends' => ['$Factory', null, '$BarracksOnTechLab', '$TechLabOnBarracks'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnFactoryToStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Factory to Starport",
			'$type' => Morph,
			'$prerequisites' => ['$FactoryOnTechLab', '$TechLabOnFactory', '$Starport'],
			'$expends' => ['$Factory', null, '$StarportOnTechLab', '$TechLabOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnStarportToBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Starport to Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$StarportOnTechLab', '$TechLabOnStarport', '$Barracks'],
			'$expends' => ['$Starport', null, '$BarracksOnTechLab', '$TechLabOnBarracks'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnStarportToFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Starport to Factory",
			'$type' => Morph,
			'$prerequisites' => ['$StarportOnTechLab', '$TechLabOnStarport', '$Factory'],
			'$expends' => ['$Starport', null, '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnBarracksWithTechLabFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Barracks with Tech Lab on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$expends' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnBarracksWithTechLabStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Barracks with Tech Lab on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$expends' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		'$SwapReactorOnFactoryWithTechLabBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Factory with Tech Lab on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$expends' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnFactoryWithTechLabStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Factory with Tech Lab on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$StarportOnTechLab', '$TechLabOnStarport', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$expends' => ['$StarportOnReactor', '$ReactorOnStarport', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnStarportWithTechLabBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Starport with Tech Lab on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$StarportOnReactor', '$ReactorOnStarport'],
			'$expends' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapReactorOnStarportWithTechLabFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Reactor on Starport with Tech Lab on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$FactoryOnTechLab', '$TechLabOnFactory', '$StarportOnReactor', '$ReactorOnStarport'],
			'$expends' => ['$FactoryOnReactor', '$ReactorOnFactory', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnBarracksWithReactorFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Barracks with Reactor on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$expends' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnBarracksWithReactorStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Barracks with Reactor on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$StarportOnReactor', '$ReactorOnStarport'],
			'$expends' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnFactoryWithReactorBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Factory with Reactor on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$expends' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnFactoryWithReactorStarport' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Factory with Reactor on Starport",
			'$type' => Morph,
			'$prerequisites' => ['$StarportOnReactor', '$ReactorOnStarport', '$FactoryOnTechLab', '$TechLabOnFactory'],
			'$expends' => ['$StarportOnTechLab', '$TechLabOnStarport', '$FactoryOnReactor', '$ReactorOnFactory'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnStarportWithReactorBarracks' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Starport with Reactor on Barracks",
			'$type' => Morph,
			'$prerequisites' => ['$BarracksOnReactor', '$ReactorOnBarracks', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$expends' => ['$BarracksOnTechLab', '$TechLabOnBarracks', '$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
		
		'$SwapTechLabOnStarportWithReactorFactory' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Swap Tech Lab on Starport with Reactor on Factory",
			'$type' => Morph,
			'$prerequisites' => ['$FactoryOnReactor', '$ReactorOnFactory', '$StarportOnTechLab', '$TechLabOnStarport'],
			'$expends' => ['$FactoryOnTechLab', '$TechLabOnFactory', '$StarportOnReactor', '$ReactorOnStarport'],
			'$yeilds' => [],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => ADDON_SWAP_TIME,
		],
	];
		
	// Terran, Units
	
	$TerranUnits = [
		'$ScoutingWorker' => [
			'$race' => null,
			'$commander' => null,
			'$name' => "Scouting Worker",
			'$type' => null,
			'$prerequisites' => [],
			'$expends' => [],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$timeCost' => null,
			'$expends' => false,
		],
		
		'$SCV' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "SCV",
			'$type' => Unit | Worker,
			'$prerequisites' => [],
			'$expends' => ['$CommandCenter', '$OrbitalCommand', '$PlanetaryFortress'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 0],
			'$timeCost' => 17,
		],
		
		'$Marine' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Marine",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Barracks', '$BarracksOnReactor', '$ReactorOnBarracks', '$BarracksOnTechLab'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 0],
			'$timeCost' => 25,
		],
		
		'$Marauder' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Marauder",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$BarracksOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 25],
			'$timeCost' => 30,
		],
		
		'$Reaper' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Reaper",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$BarracksOnTechLab'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 50],
			'$timeCost' => 45,
		],
		
		'$Ghost' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Ghost",
			'$type' => Unit,
			'$prerequisites' => ['$GhostAcademy'],
			'$expends' => ['$BarracksOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 100],
			'$timeCost' => 40,
		],
		
		'$Hellion' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Hellion",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Factory', '$FactoryOnReactor', '$ReactorOnFactory', '$FactoryOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 0],
			'$timeCost' => 30,
		],
		
		'$SiegeTank' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Siege Tank",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$FactoryOnTechLab'],
			'$supplyCost' => 3,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 125],
			'$timeCost' => 45,
		],
		
		'$Thor' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Thor",
			'$type' => Unit,
			'$prerequisites' => ['$Armory'],
			'$expends' => ['$FactoryOnTechLab'],
			'$supplyCost' => 6,
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 200],
			'$timeCost' => 60,
		],
		
		'$Viking' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Viking",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Starport', '$StarportOnReactor', '$ReactorOnStarport', '$StarportOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 75],
			'$timeCost' => 42,
		],
		
		'$Medivac' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Medivac",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$Starport', '$StarportOnReactor', '$ReactorOnStarport', '$StarportOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 42,
		],
		
		'$Banshee' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Banshee",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$StarportOnTechLab'],
			'$supplyCost' => 3,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 100],
			'$timeCost' => 60,
		],
		
		'$Raven' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Raven",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => ['$StarportOnTechLab'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 200],
			'$timeCost' => 60,
		],
		
		'$Battlecruiser' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "Battlecruiser",
			'$type' => Unit,
			'$prerequisites' => ['$FusionCore'],
			'$expends' => ['$StarportOnTechLab'],
			'$supplyCost' => 6,
			'$materials' => [
				'$minerals' => 400,
				'$gas' => 300],
			'$timeCost' => 90,
		],
		
		'$TacticalNuke' => [
			'$race' => Terran,
			'$commander' => null,
			'$name' => "TacticalNuke",
			'$type' => Unit,
			'$prerequisites' => ['$Factory'],
			'$expends' => ['$GhostAcademy'],
			'$supplyCost' => 0,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$timeCost' => 60,
		],
	];
	
	$TerranAbilities = [
		'$CalldownMULE' => [
			'$name' => "Calldown: MULE",
			'$race' => Terran,
			'$commander' => null,
			'$type' => Ability,
			'$prerequisites' => null,
			'$spellCaster' => '$OrbitalCommand',
			'$energyCost' => 50,
			'$spellCooldown' => 90,
		],
		
		'$CalldownExtraSupplies' => [
			'$name' => "Calldown: Extra Supplies",
			'$race' => Terran,
			'$commander' => null,
			'$type' => Ability | Farm,
			'$prerequisites' => null,
			'$spellCaster' => '$OrbitalCommand',
			'$energyCost' => 50,
			'$spellCooldown' => 0,
			'$supplyCapacity' => 8,
		],
		
		'$ScannerSweep' => [
			'$name' => "Scanner Sweep",
			'$race' => Terran,
			'$commander' => null,
			'$type' => Ability,
			'$prerequisites' => null,
			'$spellCaster' => '$OrbitalCommand',
			'$energyCost' => 50,
			'$spellCooldown' => 12,
		],
	];
	*/
	$validTerran = array_merge($TerranStructures, $TerranUnits); // $TerranUpgrades, $TerranAbilities, $TerranMorphs);
	