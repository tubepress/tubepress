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
class tubepress_addons_core_impl_ioc_RegisterListenersCompilerPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_ioc_RegisterListenersCompilerPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcherDefinition;

    public function onSetup()
    {
        $this->_sut           = new tubepress_addons_core_impl_ioc_RegisterListenersCompilerPass();
        $this->_mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerInterface');
        $this->_mockEventDispatcherDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_Definition');
    }

    public function testNoDispatcherService()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn(false);
        $this->_sut->process($this->_mockContainer);
        $this->assertTrue(true);
    }

    public function testNoEventInTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Service "foo" must define the "event" attribute on "tubepress.event.listener" tags');

        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn($this->_mockEventDispatcherDefinition);
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER)->andReturn(array('foo' => array(array('method' => 'some method'))));

        $this->_sut->process($this->_mockContainer);
    }

    public function testNoMethod()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn($this->_mockEventDispatcherDefinition);
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER)->andReturn(array('foo' => array(array('event' => 'some event'))));

        $this->_mockEventDispatcherDefinition->shouldReceive('addMethodCall')->once()->with('addListenerService', array('some event', array('foo', 'onSomeEvent'), 0));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testNoPriority()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn($this->_mockEventDispatcherDefinition);
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER)->andReturn(array('foo' => array(array('method' => 'some method', 'event' => 'some event'))));

        $this->_mockEventDispatcherDefinition->shouldReceive('addMethodCall')->once()->with('addListenerService', array('some event', array('foo', 'some method'), 0));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testFullyPopulatedListener()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn(true);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with(tubepress_api_event_EventDispatcherInterface::_)->andReturn($this->_mockEventDispatcherDefinition);
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER)->andReturn(array('foo' => array(array('method' => 'some method', 'event' => 'some event', 'priority' => 5))));

        $this->_mockEventDispatcherDefinition->shouldReceive('addMethodCall')->once()->with('addListenerService', array('some event', array('foo', 'some method'), 5));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }
}