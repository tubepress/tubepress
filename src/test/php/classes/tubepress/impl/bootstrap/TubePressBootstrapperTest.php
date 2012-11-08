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

    function onSetup()
    {
        $this->_sut = new tubepress_impl_bootstrap_TubePressBootstrapper();

        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager      = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockPluginDiscoverer    = $this->createMockSingletonService(tubepress_spi_plugin_PluginDiscoverer::_);
        $this->_mockPluginRegistry      = $this->createMockSingletonService(tubepress_spi_plugin_PluginRegistry::_);

        $this->_sut->setIocContainer($this->getMockIocContainer());
    }

    public static function setUpBeforeClass()
    {
        require_once TUBEPRESS_ROOT . '/src/test/resources/plugins/FakeCompilerPass.php';
        require_once TUBEPRESS_ROOT . '/src/test/resources/plugins/FakeExtension.php';
    }

    function testBoot()
    {
        $mockPlugin1 = Mockery::mock(tubepress_spi_plugin_Plugin::_);
        $mockPlugin2 = Mockery::mock(tubepress_spi_plugin_Plugin::_);
        $mockPlugin1->shouldReceive('getName')->andReturn('mock plugin 1');
        $mockPlugin2->shouldReceive('getName')->andReturn('mock plugin 2');
        $mockPlugin1->shouldReceive('getAbsolutePathOfDirectory')->once()->andReturn('xyz');

        $mockPlugin1IocContainerExtensions = array('FakeExtension', 'bogus class');
        $mockPlugin2IocCompilerPasses = array('FakeCompilerPass', 'no such class');

        $mockPlugin1->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockPlugin1IocContainerExtensions);
        $mockPlugin1->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn(array());
        $mockPlugin2->shouldReceive('getIocContainerExtensions')->once()->andReturn(array());
        $mockPlugin2->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn($mockPlugin2IocCompilerPasses);
        $mockPlugin1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('some root'));
        $mockPlugin2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user-content-dir>>');

        $this->_mockPluginDiscoverer->shouldReceive('findPluginsRecursivelyInDirectory')->once()
            ->with(TUBEPRESS_ROOT . '/src/main/php/plugins')->andReturn(array($mockPlugin1));

        $this->_mockPluginDiscoverer->shouldReceive('findPluginsRecursivelyInDirectory')->once()
            ->with(realpath('<<user-content-dir>>/plugins'))->andReturn(array($mockPlugin2));

        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin1);
        $this->_mockPluginRegistry->shouldReceive('load')->once()->with($mockPlugin2);

        $this->getMockIocContainer()->shouldReceive('addCompilerPass')->once()->with(Mockery::type('FakeCompilerPass'));
        $this->getMockIocContainer()->shouldReceive('registerExtension')->once()->with(Mockery::type('FakeExtension'));
        $this->getMockIocContainer()->shouldReceive('compile')->once();

        $this->_sut->boot();

        /*
         * Try booting twice.
         */
        $this->_sut->boot();

        $this->assertTrue(true);
    }
}