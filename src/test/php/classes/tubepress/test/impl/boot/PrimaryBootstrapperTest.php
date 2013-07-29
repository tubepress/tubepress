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

/**
 * @covers tubepress_impl_boot_PrimaryBootstrapper<extended>
 */
class tubepress_test_impl_bootstrap_PrimaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_PrimaryBootstrapper
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
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoadingHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonDiscoverer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonBooter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocContainerBootHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCoreIocContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_boot_PrimaryBootstrapper();

        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockClassLoadingHelper          = $this->createMockSingletonService(tubepress_spi_boot_ClassLoadingHelper::_);
        $this->_mockAddonDiscoverer             = $this->createMockSingletonService(tubepress_spi_boot_AddonDiscoverer::_);
        $this->_mockIocContainerBootHelper      = $this->createMockSingletonService(tubepress_spi_boot_IocContainerHelper::_);
        $this->_mockCoreIocContainer            = ehough_mockery_Mockery::mock('tubepress_impl_ioc_CoreIocContainer');
        $this->_mockAddonBooter                 = $this->createMockSingletonService(tubepress_spi_boot_AddonBooter::_);

        $this->_sut->setIocContainer($this->_mockCoreIocContainer);
    }

    public function onTearDown()
    {
        $nullHandler = new ehough_epilog_handler_NullHandler();

        ehough_epilog_LoggerFactory::setHandlerStack(array($nullHandler));
    }

    public function testBoot()
    {
        $this->_mockClassLoadingHelper->shouldReceive('prime')->once()->with(ehough_mockery_Mockery::any('ehough_pulsar_ComposerClassLoader'));
        $this->_mockClassLoadingHelper->shouldReceive('addClassHintsForAddons')->once()->with(array('x'), ehough_mockery_Mockery::any('ehough_pulsar_ComposerClassLoader'));
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);
        $this->_mockAddonDiscoverer->shouldReceive('findAddons')->once()->andReturn(array('x'));
        $this->_mockIocContainerBootHelper->shouldReceive('compile')->once()->with($this->_mockCoreIocContainer, array('x'));
        $this->_mockAddonBooter->shouldReceive('boot')->once()->with(array('x'));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::BOOT_COMPLETE);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn('true');

        $result = $this->_sut->boot();

        $this->assertTrue($result);
    }
}