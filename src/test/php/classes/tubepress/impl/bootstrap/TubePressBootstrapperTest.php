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
class tubepress_impl_bootstrap_TubePressBootstrapperTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockStorageManager;

    private $_mockPluginDiscoverer;

    private $_mockPluginRegistry;

    function setUp()
    {
        $this->_sut = new tubepress_impl_bootstrap_TubePressBootstrapper();

        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager      = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockPluginDiscoverer    = Mockery::mock(tubepress_spi_plugin_PluginDiscoverer::_);
        $this->_mockPluginRegistry      = Mockery::mock(tubepress_spi_plugin_PluginRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPluginDiscoverer($this->_mockPluginDiscoverer);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setPluginRegistry($this->_mockPluginRegistry);
    }

    function testBoot()
    {
        $mockPlugin1 = Mockery::mock(tubepress_spi_plugin_Plugin::_);
        $mockPlugin2 = Mockery::mock(tubepress_spi_plugin_Plugin::_);
        $mockPlugin1->shouldReceive('getName')->andReturn('mock plugin 1');
        $mockPlugin2->shouldReceive('getName')->andReturn('mock plugin 2');

        $mockPlugin1IocContainerExtensions = array('FakeExtension');

        $mockPlugin1->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockPlugin1IocContainerExtensions);
        $mockPlugin2->shouldReceive('getIocContainerExtensions')->once()->andReturn(array());
        $mockPlugin1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());
        $mockPlugin2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user-content-dir>>');

        $this->_mockPluginDiscoverer->shouldReceive('findPluginsNonRecursivelyInDirectory')->once()
            ->with(TUBEPRESS_ROOT . '/src/main/php/plugins/core')->andReturn(array($mockPlugin1));

        $this->_mockPluginDiscoverer->shouldReceive('findPluginsRecursivelyInDirectory')->once()
            ->with(TUBEPRESS_ROOT . '/src/main/php/plugins/addon')->andReturn(array());

        $this->_mockPluginDiscoverer->shouldReceive('findPluginsRecursivelyInDirectory')->once()
            ->with(realpath('<<user-content-dir>>/plugins'))->andReturn(array($mockPlugin2));

        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin1);
        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin2);

        $this->_sut->boot();

        $this->assertTrue(true);
    }
}