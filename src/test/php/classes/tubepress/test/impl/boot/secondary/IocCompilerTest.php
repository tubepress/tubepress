<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_boot_secondary_IocCompiler<extended>
 */
class tubepress_test_impl_boot_secondary_IocCompilerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_secondary_IocCompiler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocContainer;

    public function onSetup()
    {
        $this->_sut                          = new tubepress_impl_boot_secondary_IocCompiler();
        $this->_mockIocContainer             = ehough_mockery_Mockery::mock('tubepress_impl_ioc_IconicContainerBuilder');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once TUBEPRESS_ROOT . '/src/test/resources/fixtures/classes/tubepress/test/impl/ioc/FakeCompilerPass.php';
        require_once TUBEPRESS_ROOT . '/src/test/resources/fixtures/classes/tubepress/test/impl/ioc/FakeExtension.php';
    }

    public function testCompile()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1IocContainerExtensions = array('FakeExtension', 'bogus class');
        $mockAddon2IocContainerExtensions = array('Hello', 'There');
        $mockAddon2IocCompilerPasses = array('FakeCompilerPass', 'no such class');

        $mockAddon1->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockAddon1IocContainerExtensions);
        $mockAddon1->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockAddon2IocContainerExtensions);
        $mockAddon2->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn($mockAddon2IocCompilerPasses);

        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockIocContainer->shouldReceive('compile')->once();
        $this->_mockIocContainer->shouldReceive('addCompilerPass')->once()->with(ehough_mockery_Mockery::any('FakeCompilerPass'));
        $this->_mockIocContainer->shouldReceive('registerExtension')->once()->with(ehough_mockery_Mockery::any('FakeExtension'));

        $this->_sut->compile($this->_mockIocContainer, $mockAddons);

        $this->assertTrue(true);
    }

}