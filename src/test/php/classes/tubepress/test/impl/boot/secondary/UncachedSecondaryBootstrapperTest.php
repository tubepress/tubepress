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
 * @covers tubepress_impl_boot_secondary_UncachedSecondaryBootstrapper<extended>
 */
class tubepress_test_impl_boot_secondary_UncachedSecondaryBootstrapperTest extends tubepress_test_impl_boot_secondary_AbstractSecondaryBootstrapperTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonDiscoverer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoaderHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerDumper;

    public function buildSut()
    {
        $this->_mockAddonDiscoverer   = ehough_mockery_Mockery::mock(tubepress_spi_addon_AddonFinderInterface::_);
        $this->_mockIocHelper         = ehough_mockery_Mockery::mock(tubepress_spi_boot_secondary_IocCompilerInterface::_);
        $this->_mockClassLoaderHelper = ehough_mockery_Mockery::mock(tubepress_spi_boot_secondary_ClassLoaderPrimerInterface::_);
        $this->_mockContainerBuilder  = ehough_mockery_Mockery::mock('tubepress_impl_ioc_IconicContainerBuilder');
        $this->_mockContainerDumper   = ehough_mockery_Mockery::mock('ehough_iconic_dumper_DumperInterface');

        $sut = new tubepress_impl_boot_secondary_UncachedSecondaryBootstrapper(

            true,
            $this->_mockClassLoaderHelper,
            $this->_mockAddonDiscoverer,
            $this->_mockIocHelper
        );

        $sut->__setContainerBuilder($this->_mockContainerBuilder);
        $sut->__setContainerDumper($this->_mockContainerDumper);

        return $sut;
    }

    public function testGetContainer()
    {
        $this->getMockSettingsFileReader()->shouldReceive('getCachedContainerStoragePath')->once()->andReturn($this->getMockContainerPath());
        $this->getMockSettingsFileReader()->shouldReceive('isClassLoaderEnabled')->twice()->andReturn(true);
        $this->_mockClassLoaderHelper->shouldReceive('prime')->once()->with($this->getMockClassLoader());
        $this->getMockSettingsFileReader()->shouldReceive('getAddonBlacklistArray')->once()->andReturn(array('hello there'));
        $mockAddons = $this->createMockAddonArray();
        $this->_mockAddonDiscoverer->shouldReceive('findAddons')->once()->with(array('hello there'))->andReturn($mockAddons);
        $this->_mockClassLoaderHelper->shouldReceive('addClassHintsForAddons')->once()->with($mockAddons, $this->getMockClassLoader());
        $this->_mockIocHelper->shouldReceive('compile')->once()->with($this->_mockContainerBuilder, $mockAddons);
        $this->_mockContainerBuilder->shouldReceive('setParameter')->once()->with('classMap', array());
        $mockIconicContainerBuilder = ehough_mockery_Mockery::mock('ehough_iconic_ContainerBuilder');
        $this->_mockContainerBuilder->shouldReceive('getDelegateIconicContainerBuilder')->once()->andReturn($mockIconicContainerBuilder);
        $this->_mockContainerDumper->shouldReceive('dump')->once()->with(array('class' => 'TubePressServiceContainer'))->andReturn('xyz');

        $container = $this->getContainer();

        $this->assertInstanceOf('TubePressServiceContainer', $container);
    }

    private function createMockAddonArray()
    {
        return array(

            ehough_mockery_Mockery::mock('tubepress_spi_addon_AddonInterface')
        );
    }
}