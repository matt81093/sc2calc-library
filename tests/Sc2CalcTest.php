<?php
/**
 * This file is part of the holonet db migration package
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\dbmigrate\tests\schema;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the parser / timeline / scheduler using the example builds of the old application.
 */
class Sc2CalcTest extends TestCase {
    /**
     * @param string $testBuildOrder Test build order string from the test data directory
     * @dataProvider buildTestData()
     * @uses         \holonet\sc2calc\timeline\Scheduler
     * @covers       \holonet\sc2calc\timeline\Scheduler
     * @uses         \holonet\sc2calc\timeline\Timeline
     * @covers       \holonet\sc2calc\timeline\Timeline
     * @uses         \holonet\sc2calc\Sc2Calc
     * @covers       \holonet\sc2calc\Sc2Calc::fromBuildOrderString()
     */
	public function testFromBuildOrderString(string $testBuildOrder): void {
        $sc2calc = new \holonet\sc2calc\Sc2Calc();
        $sc2calc->fromBuildOrderString($testBuildOrder);

        //if it ran through without an exception, just mark the test as "asserted"
        $this->assertTrue(true);
    }

    /**
     * @return array with all the build strings loaded from the filesystem
     */
    public function buildTestData(): array {
        $testBuildOrderStrings = array();
        $it = new FilesystemIterator(__DIR__.'/orders/');
        foreach ($it as $testBuildOrderFile) {
            $testBuildOrderStrings[str_replace('.txt', '', basename($testBuildOrderFile))] = array(file_get_contents($testBuildOrderFile));
        }
        return $testBuildOrderStrings;
    }
}
