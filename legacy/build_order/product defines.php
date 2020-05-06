<?php

define("ADDON_SWAP_TIME", 10);
define("CHRONO_BOOST_RATE", 1.5);
define("CHRONO_BOOST_HUMAN_DELAY", 0.1);
define("ENERGY_RATE", 0.5625);
define("LARVA_TIME", 15);
define("MULE_MINING", 2.9);
define("WARPGATE_QUEUE_REDUCTION", 10);
define("STUKOV_CC_LARVA_RATE", 16);
define("STUKOV_BUNKER_LARVA_RATE", 20);

/**
 * Flags for product types.
 */
 
 // Race and commander defines
define("Terran", 1);
define("Zerg", 2);
define("Protoss", 4);
define("Stukov", 6);

// Product types
define("Unit", 8);
define("Structure", 16);
define("Upgrade", 32);
define("Morph", 64);
define("Base", 128);
define("Ability", 256);
define("Spellcaster", 512);

// Special product defines
define("Worker", 1024);
define("Geyser", 2048);
define("Booster", 4096);
define("Farm", 8192);
?>