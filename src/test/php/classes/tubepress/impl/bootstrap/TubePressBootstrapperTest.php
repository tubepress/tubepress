<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_bootstrap_TubePressBootstrapperTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockStorageManager;

    private $_mockPluginDiscoverer;

    private $_mockPluginRegistry;

    private $_mockEventDispatcher;

    function setUp()
    {
        $this->_sut = new tubepress_impl_bootstrap_TubePressBootstrapper();

        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager      = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockPluginDiscoverer    = Mockery::mock(tubepress_spi_plugin_PluginDiscoverer::_);
        $this->_mockPluginRegistry      = Mockery::mock(tubepress_spi_plugin_PluginRegistry::_);
        $this->_mockEventDispatcher     = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPluginDiscoverer($this->_mockPluginDiscoverer);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPluginRegistry($this->_mockPluginRegistry);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
    }

    function testBoot()
    {
        $mockPlugin1 = Mockery::mock(tubepress_spi_plugin_Plugin::_);
        $mockPlugin2 = Mockery::mock(tubepress_spi_plugin_Plugin::_);

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user-content-dir>>');
        $this->_mockPluginDiscoverer->shouldReceive('findPluginsNonRecursivelyInDirectory')->once()
            ->with(realpath(__DIR__ . '/../../../../../../main/php/plugins/tubepress/plugins/core'))->andReturn(array($mockPlugin1));
        $this->_mockPluginDiscoverer->shouldReceive('findPluginsNonRecursivelyInDirectory')->once()
            ->with(realpath(__DIR__ . '/../../../../../../main/php/plugins/tubepress/plugins/wordpresscore'))->andReturn(array());
        $this->_mockPluginDiscoverer->shouldReceive('findPluginsNonRecursivelyInDirectory')->once()
            ->with(realpath('<<user-content-dir>>/plugins'))->andReturn(array($mockPlugin2));
        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin1);
        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin2);


        $this->_mockEventDispatcher->shouldReceive('dispatchWithoutEventInstance')->once()->with(tubepress_api_const_event_CoreEventNames::BOOT);

        $this->_sut->boot();

        $this->assertTrue(true);
    }
}