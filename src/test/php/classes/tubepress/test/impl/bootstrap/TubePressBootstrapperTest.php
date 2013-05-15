<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_bootstrap_TubePressBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_bootstrap_TubePressBootstrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonDiscoverer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonRegistry;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_bootstrap_TubePressBootstrapper();

        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockAddonDiscoverer             = $this->createMockSingletonService(tubepress_spi_addon_AddonDiscoverer::_);
        $this->_mockAddonRegistry               = $this->createMockSingletonService(tubepress_spi_addon_AddonLoader::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_sut->setIocContainer($this->getMockIocContainer());
    }

    public function onTearDown()
    {
        $nullHandler = new ehough_epilog_handler_NullHandler();

        ehough_epilog_LoggerFactory::setHandlerStack(array($nullHandler));
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once TUBEPRESS_ROOT . '/src/test/resources/addons/FakeCompilerPass.php';
        require_once TUBEPRESS_ROOT . '/src/test/resources/addons/FakeExtension.php';
    }

    public function testBoot()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1IocContainerExtensions = array('FakeExtension', 'bogus class');
        $mockAddon2IocCompilerPasses = array('FakeCompilerPass', 'no such class');

        $mockAddon1->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockAddon1IocContainerExtensions);
        $mockAddon1->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getIocContainerExtensions')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn($mockAddon2IocCompilerPasses);
        $mockAddon1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('some root'));
        $mockAddon2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());
        $mockAddon1->shouldReceive('getClassMap')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getClassMap')->once()->andReturn(array('foo' => 'bar'));

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user-content-dir>>');

        $this->_mockAddonDiscoverer->shouldReceive('findAddonsInDirectory')->once()
            ->with(TUBEPRESS_ROOT . '/src/main/php/addons')->andReturn(array($mockAddon1));

        $this->_mockAddonDiscoverer->shouldReceive('findAddonsInDirectory')->once()
            ->with(realpath('<<user-content-dir>>/addons'))->andReturn(array($mockAddon2));

        $this->_mockAddonRegistry->shouldReceive('load')->once()->with($mockAddon1);
        $this->_mockAddonRegistry->shouldReceive('load')->once()->with($mockAddon2);

        $this->getMockIocContainer()->shouldReceive('addCompilerPass')->once()->with(ehough_mockery_Mockery::type('FakeCompilerPass'));
        $this->getMockIocContainer()->shouldReceive('registerExtension')->once()->with(ehough_mockery_Mockery::type('FakeExtension'));
        $this->getMockIocContainer()->shouldReceive('compile')->once();

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('false');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::BOOT_COMPLETE);

        $this->_sut->boot(new ehough_pulsar_ComposerClassLoader(dirname(__FILE__) . '/../../../../../../../../vendor'));

        /*
         * Try booting twice.
         */
        $this->_sut->boot(new ehough_pulsar_ComposerClassLoader(dirname(__FILE__) . '/../../../../../../../../vendor'));

        $this->assertTrue(true);
    }
}