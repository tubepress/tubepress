<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @covers tubepress_internal_boot_InitialBootstrapper<extended>
 */
class tubepress_test_internal_boot_InitialBootstrapperTest extends PHPUnit_Framework_TestCase
{
    public function testGetServiceContainer()
    {
        $mockBootstrapper = Mockery::mock('tubepress_internal_boot_PrimaryBootstrapper');
        $mockContainer    = Mockery::mock('tubepress_api_ioc_ContainerInterface');

        tubepress_internal_boot_InitialBootstrapper::__setPrimaryBootstrapper($mockBootstrapper);

        $mockBootstrapper->shouldReceive('getServiceContainer')->once()->andReturn($mockContainer);

        $this->assertFalse(defined('TUBEPRESS_ROOT'));
        $this->assertFalse(defined('TUBEPRESS_VERSION'));

        $actual1 = tubepress_internal_boot_InitialBootstrapper::getServiceContainer();
        $actual2 = tubepress_internal_boot_InitialBootstrapper::getServiceContainer();

        $this->assertSame($mockContainer, $actual1);
        $this->assertSame($mockContainer, $actual2);

        $this->assertTrue(defined('TUBEPRESS_ROOT'));
        $this->assertTrue(defined('TUBEPRESS_VERSION'));

        $actualTubePressRoot   = realpath(TUBEPRESS_ROOT);
        $expectedTubePressRoot = realpath(__DIR__ . '/../../../../../../../..');

        $this->assertTrue(is_dir($actualTubePressRoot));
        $this->assertTrue(is_dir($expectedTubePressRoot));

        $this->assertEquals($expectedTubePressRoot, $actualTubePressRoot);

        $this->assertEquals('99.99.99', TUBEPRESS_VERSION);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}