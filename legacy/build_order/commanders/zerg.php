<?php

	// Zerg Structures
	
	$ZergStructures = [
		'$Hatchery' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Hatchery",
			'$type' => Structure | Base | Farm,
			'$materials' => [
				'$minerals' => 300, 
				'$gas' => 0],
			'$build_time' => 100,
			'$prerequisites' => [],
			'$supplyCapacity' => 2,
		],
		
		'$Extractor' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Extractor",
			'$type' => Structure | Geyser,
			'$materials' => [
				'$minerals' => 25, 
				'$gas' => 0],
			'$build_time' => 30,
			'$prerequisites' => [],
			
		],
		
		'$SpawningPool' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Spawning Pool",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 200, 
				'$gas' => 0],
			'$build_time' => 65,
			'$prerequisites' => ['$Hatchery'],
			
		],
		
		'$EvolutionChamber' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Evolution Chamber",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 75, 
				'$gas' => 0],
			'$build_time' => 35,
			'$prerequisites' => ['$Hatchery'],
			
		],
		
		'$SpineCrawler' => [ 
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Spine Crawler",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 0],
			'$build_time' => 50,
			'$prerequisites' => ['$SpawningPool'],
			
		],
		
		'$SporeCrawler' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Spore Crawler",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 75, 
				'$gas' => 0],
			'$build_time' => 30,
			'$prerequisites' => ['$EvolutionChamber'],
			
		],
		
		'$RoachWarren' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Roach Warren",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 0],
			'$build_time' => 55,
			'$prerequisites' => ['$SpawningPool'],
			
		],
		
		'$BanelingNest' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Baneling Nest",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 50],
			'$build_time' => 60,
			'$prerequisites' => ['$SpawningPool'],
			
		],
		
		'$Lair' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Lair",
			'$type' => Structure | Base | Farm,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$build_time' => null,
			'$prerequisites' => [],
			
			'$supplyCapacity' => 2,
		],
		
		'$HydraliskDen' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Hydralisk Den",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 100],
			'$build_time' => 40,
			'$prerequisites' => ['$Lair'],
			
		],
		
		'$InfestationPit' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Infestation Pit",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 100],
			'$build_time' => 50,
			'$prerequisites' => ['$Lair'],
			
		],
		
		'$Spire' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Spire",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 200, 
				'$gas' => 200],
			'$build_time' => 100,
			'$prerequisites' => ['$Lair'],
			
		],
		
		'$NydusNetwork' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Nydus Network",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 155, 
				'$gas' => 200],
			'$build_time' => 50,
			'$prerequisites' => ['$Lair'],
			
		],
		
		'$NydusWorm' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Nydus Worm",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 100, 
				'$gas' => 100],
			'$build_time' => 20,
			'$prerequisites' => ['$NydusNetwork'],
			
		],
		
		'$Hive' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Hive",
			'$type' => Structure | Base | Farm,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$build_time' => null,
			'$prerequisites' => [],
			'$supplyCapacity' => 2,
		],
		
		'$UltraliskCavern' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ultralisk Cavern",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => 150, 
				'$gas' => 200],
			'$build_time' => 65,
			'$prerequisites' => ['$Hive'],
			
		],
		
		'$GreaterSpire' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Greater Spire",
			'$type' => Structure,
			'$materials' => [
				'$minerals' => null, 
				'$gas' => null],
			'$build_time' => null,
			'$prerequisites' => [],
			'$supplyCost' => null
		],
	];
	
	// Zerg Upgrades
	
	$ZergUpgrades = [
		'$MeleeAttackslevel1' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Melee Attacks Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$MeleeAttackslevel2' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Melee Attacks Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 190,
		],
		
		'$MeleeAttackslevel3' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Melee Attacks Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 220,
		],
		
		'$MissileAttackslevel1' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Missile Attacks Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$MissileAttackslevel2' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Missile Attacks Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 190,
		],
		
		'$MissileAttackslevel3' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Missile Attacks Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 220,
		],
		
		'$FlyerAttackslevel1' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Attacks Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 160,
		],
		
		'$FlyerAttackslevel2' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Attacks Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 175,
				'$gas' => 175],
			'$build_time' => 190,
		],
		
		'$FlyerAttackslevel3' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Attacks Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 250,
				'$gas' => 250],
			'$build_time' => 220,
		],
		
		'$GroundCarapacelevel1' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ground Carapace Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 160,
		],
		
		'$GroundCarapacelevel2' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ground Carapace Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 225,
				'$gas' => 225],
			'$build_time' => 190,
		],
		
		'$GroundCarapacelevel3' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ground Carapace Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$EvolutionChamber'],
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 300],
			'$build_time' => 220,
		],
		
		'$FlyerCarapacelevel1' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Carapace Level 1",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 160,
		],
		
		'$FlyerCarapacelevel2' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Carapace Level 2",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 225,
				'$gas' => 225],
			'$build_time' => 190,
		],
		
		'$FlyerCarapacelevel3' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Flyer Carapace Level 3",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$Spire', '$GreaterSpire'],
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 300],
			'$build_time' => 220,
		],
		
		'$CentrifugalHooks' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Centrifugal Hooks",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$BanelingNest'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$GlialReconstitution' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Glial Reconstitution",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$RoachWarren'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 110,
		],
		
		'$MetabolicBoost' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Metabolic Boost",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$SpawningPool'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 110,
		],
		
		'$PneumatizedCarapace' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Pneumatized Carapace",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Hatchery', '$Lair', '$Hive'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 60,
		],
			
		'$GroovedSpines' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Grooved Spines",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$HydraliskDen'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 80,
		],
		
		'$NeuralParasite' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Neural Parasite",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$InfestationPit'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$PathogenGlands' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Pathogen Glands",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$InfestationPit'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 80,
		],
		
		'$AdrenalGlands' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Adrenal Glands",
			'$type' => Upgrade,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$SpawningPool'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 130,
		],
		
		'$VentralSacs' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ventral Sacs",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Hatchery', '$Lair', '$Hive'],
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 200],
			'$build_time' => 130,
		],
		
		'$TunnelingClaws' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Tunneling Claws",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$RoachWarren'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$ChitinousPlating' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Chitinous Plating",
			'$type' => Upgrade,
			'$prerequisites' => [],
			'$expends' => ['$UltraliskCavern'],
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 110,
		],
		
		'$Burrow' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Burrow",
			'$type' => Upgrade,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Hatchery', '$Lair', '$Hive'],
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 100,
		],
	];
	
	$ZergUnits = [
		'$larva' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "larva",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => [],
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$build_time' => null,
		], 
		
		'$Drone' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Drone",
			'$type' => Unit | Worker,
			'$prerequisites' => [],
			'$expends' => ['$larva'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 0],
			'$build_time' => 17,
		],
		
		'$Overlord' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Overlord",
			'$type' => Unit | Farm,
			'$prerequisites' => [],
			'$expends' => ['$larva'],
			'$supplyCost' => 0,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 0],
			'$build_time' => 25,
			'$supplyCapacity' => 8,
		],
		
		'$Queen' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Queen",
			'$type' => Unit | Spellcaster,
			'$prerequisites' => ['$SpawningPool'],
			'$expends' => ['$Hatchery', '$Lair', '$Hive'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 0],
			'$build_time' => 50,
			'$larvaCost' => 0,
			'$energyStart' => 25,
			'$energyMax' => 200,
		],
		
		'$Zergling' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Zergling",
			'$type' => Unit,
			'$prerequisites' => ['$SpawningPool'],
			'$expends' => ['$larva'],
			'$supplyCost' => 1,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 0],
			'$build_time' => 24,
		],
		
		'$Baneling' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Baneling",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => [],
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$build_time' => null,
		],
		
		'$Roach' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Roach",
			'$type' => Unit,
			'$prerequisites' => ['$RoachWarren'],
			'$expends' => ['$larva'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 75,
				'$gas' => 25],
			'$build_time' => 27,
		],
		
		'$Overseer' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Overseer",
			'$type' => Unit | Farm,
			'$prerequisites' => [],
			'$expends' => [],
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$build_time' => null,
			'$supplyCapacity' => 8,
		],
		
		'$Hydralisk' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Hydralisk",
			'$type' => Unit,
			'$prerequisites' => ['$HydraliskDen'],
			'$expends' => ['$larva'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 50],
			'$build_time' => 33,
		],
		
		'$Mutalisk' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Mutalisk",
			'$type' => Unit,
			'$prerequisites' => ['$Spire'],
			'$expends' => ['$larva'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 100],
			'$build_time' => 33,
		],
		
		'$Corruptor' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Corruptor",
			'$type' => Unit,
			'$prerequisites' => ['$Spire'],
			'$expends' => ['$larva'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 100],
			'$build_time' => 40,
		],
		
		'$BroodLord' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Brood Lord",
			'$type' => Unit,
			'$prerequisites' => [],
			'$expends' => [],
			
			'$materials' => [
				'$minerals' => null,
				'$gas' => null],
			'$build_time' => null,
		],
		
		'$Infestor' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Infestor",
			'$type' => Unit,
			'$prerequisites' => ['$InfestationPit'],
			'$expends' => ['$larva'],
			'$supplyCost' => 2,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 150],
			'$build_time' => 50,
		],
		
		'$Ultralisk' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Ultralisk",
			'$type' => Unit,
			'$prerequisites' => ['$UltraliskCavern'],
			'$expends' => ['$larva'],
			'$supplyCost' => 6,
			'$materials' => [
				'$minerals' => 300,
				'$gas' => 200],
			'$build_time' => 70,
		],
	];
	
	// Zerg Morphs
	
	$ZergMorphs = [
	
		// Structure Morphs
		
		'$MorphToLair' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Lair",
			'$type' => Morph,
			'$prerequisites' => ['$SpawningPool'],
			'$expends' => ['$Hatchery'],
			'$yeilds' => ['$Lair'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 100],
			'$build_time' => 80,
		],
		
		'$MorphToHive' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Hive",
			'$type' => Morph,
			'$prerequisites' => ['$InfestationPit'],
			'$expends' => ['$Lair'],
			'$yeilds' => ['$Hive'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 200,
				'$gas' => 150],
			'$build_time' => 100,
		],
		
		'$MorphToGreaterSpire' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Greater Spire",
			'$type' => Morph,
			'$prerequisites' => ['$Hive'],
			'$expends' => ['$Spire'],
			'$yeilds' => ['$GreaterSpire'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 100,
				'$gas' => 150],
			'$build_time' => 100,
		],
		
		// Unit Morphs
		
		'$MorphToBaneling' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Baneling",
			'$type' => Morph,
			'$prerequisites' => ['$BanelingNest'],
			'$expends' => ['$Zergling'],
			'$yeilds' => ['$Lair'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 25,
				'$gas' => 25],
			'$build_time' => 20,
		],
		
		'$MorphToOverseer' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Overseer",
			'$type' => Morph,
			'$prerequisites' => ['$Lair'],
			'$expends' => ['$Overlord'],
			'$yeilds' => ['$Lair'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 50,
				'$gas' => 25],
			'$build_time' => 20,
		],
		
		'$MorphToBroodLord' => [
			'$race' => Zerg,
			'$commander' => null,
			'$name' => "Brood Lord",
			'$type' => Morph,
			'$prerequisites' => ['$GreaterSpire'],
			'$expends' => ['$Mutalisk'],
			'$yeilds' => ['$BroodLord'],
			'$supplyCost' => null,
			'$materials' => [
				'$minerals' => 150,
				'$gas' => 150],
			'$build_time' => 34,
		],
	];
	
	$ZergAbilities = [
		'$SpawnLarvae' => [
			'$name' => "Spawn Larvae",
			'$race' => Zerg,
			'$commander' => null,
			'$type' => Ability,
			'$prerequisites' => null,
			'$spellCaster' => '$Queen',
			'$energyCost' => 25,
			'$spellCooldown' => 40,
		],
		
		'$CreepTumor' => [
			'$name' => "Creep Tumor",
			'$race' => Zerg,
			'$commander' => null,
			'$type' => Ability,
			'$prerequisites' => null,
			'$spellCaster' => '$Queen',
			'$energyCost' => 25,
			'$spellCooldown' => 15,
		],
	];
	
	$validZerg = array_merge($ZergStructures, $ZergUnits, $ZergUpgrades, $ZergAbilities, $ZergMorphs);

?>