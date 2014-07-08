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
 * @covers tubepress_wordpress_impl_Callback
 */
class tubepress_test_wordpress_impl_CallbackTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var tubepress_wordpress_impl_Callback
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockActivationHook;

    public function onSetup()
    {
        $mockEnvironmentDetector    = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);
        $mockWpFunctions            = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEventDispatcher = $this->mock('tubepress_lib_event_api_EventDispatcherInterface');
        $this->_mockActivationHook  = $this->mock('tubepress_wordpress_impl_wp_ActivationHook');

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('booya');
        $mockEnvironmentDetector->shouldReceive('setBaseUrl')->once()->with('booya/plugins/tubepress');

        $this->_sut = new tubepress_wordpress_impl_Callback(

            $mockEnvironmentDetector,
            $this->_mockEventDispatcher,
            $mockWpFunctions,
            $this->_mockActivationHook
        );
    }

    public function testFilter()
    {
        $args = array(1, 'two', array('three'));

        $mockFilterEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockFilterEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(1, array('args' => array('two', array('three'))))->andReturn($mockFilterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.filter.someFilter', $mockFilterEvent);

        $result = $this->_sut->onFilter('someFilter', $args);

        $this->assertEquals('abc', $result);
    }

    public function testAction()
    {
        $mockActionEvent = $this->mock('tubepress_lib_event_api_EventInterface');

        $args = array(1, 'two', array('three'));

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($args)->andReturn($mockActionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.action.someAction', $mockActionEvent);

        $this->_sut->onAction('someAction', $args);

        $this->assertTrue(true);
    }

    public function testPluginActivation()
    {
        $this->_mockActivationHook->shouldReceive('execute')->once();

        $this->_sut->onPluginActivation();

        $this->assertTrue(true);
    }

    public function __callback($event)
    {
        return $event instanceof tubepress_lib_event_api_EventInterface
        && $event->getSubject() === array(1, 'two', array('three'));
    }
}