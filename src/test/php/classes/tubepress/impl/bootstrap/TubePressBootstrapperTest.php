<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_bootstrap_TubePressBootstrapperTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockStorageManager;

    private $_mockPluginDiscoverer;

    private $_mockPluginRegistry;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    function onSetup()
    {
        $this->_sut = new tubepress_impl_bootstrap_TubePressBootstrapper();

        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockPluginDiscoverer            = $this->createMockSingletonService(tubepress_spi_plugin_PluginDiscoverer::_);
        $this->_mockPluginRegistry              = $this->createMockSingletonService(tubepress_spi_plugin_PluginRegistry::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut->setIocContainer($this->getMockIocContainer());
    }

    function onTearDown()
    {
        $nullHandler = new ehough_epilog_impl_handler_NullHandler();

        ehough_epilog_api_LoggerFactory::setHandlerStack(array($nullHandler));
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

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('false');

        $this->_sut->boot();

        /*
         * Try booting twice.
         */
        $this->_sut->boot();

        $this->assertTrue(true);
    }
}