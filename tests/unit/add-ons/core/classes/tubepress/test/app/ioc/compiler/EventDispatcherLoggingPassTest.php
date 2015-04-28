<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_ioc_compiler_EventDispatcherLoggingPass
 */
class tubepress_test_app_ioc_compiler_EventDispatcherLoggingPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_ioc_compiler_EventDispatcherLoggingPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    public function onSetup()
    {
        $this->_sut                  = new tubepress_app_ioc_compiler_EventDispatcherLoggingPass();
        $this->_mockContainerBuilder = $this->mock(tubepress_platform_api_ioc_ContainerBuilderInterface::_);
    }

    public function testProcess()
    {
        $eventDispatcherMethodCalls = array(
            array('addListenerService',
                array(
                    'eventName',
                    array('listener-service-id', 'method'),
                    30
                ),
            ),
            array('somethingElse', array())
        );

        $eventDispatcherDefinition = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $eventDispatcherDefinition->shouldReceive('getClass')->once()->andReturn(tubepress_lib_api_event_EventDispatcherInterface::_);
        $eventDispatcherDefinition->shouldReceive('getMethodCalls')->once()->andReturn($eventDispatcherMethodCalls);

        $listenerServiceDefinition = $this->mock('stdClass');
        $listenerServiceDefinition->shouldReceive('getClass')->once()->andReturn('listener-class');

        $mockBootLogger = $this->mock('tubepress_platform_impl_log_BootLogger');
        $mockBootLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->with(tubepress_lib_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('has')->with('tubepress_platform_impl_log_BootLogger')->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('getDefinition')->once()->with(tubepress_lib_api_event_EventDispatcherInterface::_)->andReturn($eventDispatcherDefinition);
        $this->_mockContainerBuilder->shouldReceive('get')->once()->with('tubepress_platform_impl_log_BootLogger')->andReturn($mockBootLogger);
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->once()->with('listener-service-id')->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('getDefinition')->once()->with('listener-service-id')->andReturn($listenerServiceDefinition);

        $this->_sut->process($this->_mockContainerBuilder);
        $this->assertTrue(true);
    }

    public function testMissingEventDispatcher()
    {
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->with(tubepress_lib_api_event_EventDispatcherInterface::_)->andReturn(false);
        $this->_mockContainerBuilder->shouldReceive('has')->with('tubepress_platform_impl_log_BootLogger')->andReturn(true);

        $this->_sut->process($this->_mockContainerBuilder);
        $this->assertTrue(true);
    }

    public function testMissingBootLogger()
    {
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->with(tubepress_lib_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('has')->with('tubepress_platform_impl_log_BootLogger')->andReturn(false);

        $this->_sut->process($this->_mockContainerBuilder);
        $this->assertTrue(true);
    }
}