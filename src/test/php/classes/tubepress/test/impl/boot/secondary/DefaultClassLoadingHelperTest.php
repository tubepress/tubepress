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
 * @covers tubepress_impl_boot_secondary_ClassLoaderPrimer<extended>
 */
class tubepress_test_impl_boot_secondary_DefaultClassLoadingHelperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_secondary_ClassLoaderPrimer
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    public function onSetup()
    {
        $this->_sut             = new tubepress_impl_boot_secondary_ClassLoaderPrimer();
        $this->_mockClassLoader = ehough_mockery_Mockery::mock('ehough_pulsar_ComposerClassLoader');
    }

    public function testPrime()
    {
        $this->_sut->prime($this->_mockClassLoader);

        $this->assertTrue(true);
    }

    public function testAddClassHints()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonInterface::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('some root', 'prefix' => 'something'));
        $mockAddon2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());
        $mockAddon1->shouldReceive('getClassMap')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getClassMap')->once()->andReturn(array('foo' => 'bar'));
        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockClassLoader->shouldReceive('registerNamespaceFallback')->once()->with('some root');
        $this->_mockClassLoader->shouldReceive('registerPrefixFallback')->once()->with('some root');

        $this->_mockClassLoader->shouldReceive('registerPrefix')->once()->with('prefix', 'something');
        $this->_mockClassLoader->shouldReceive('registerNamespace')->once()->with('prefix', 'something');


        $this->_sut->addClassHintsForAddons($mockAddons, $this->_mockClassLoader);

        $this->assertTrue(true);
    }
}