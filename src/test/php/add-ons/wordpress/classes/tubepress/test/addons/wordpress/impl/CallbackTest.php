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
 * @covers tubepress_addons_wordpress_impl_Callback
 */
class tubepress_test_addons_wordpress_impl_CallbackTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var tubepress_addons_wordpress_impl_Callback
     */
    private $_sut;

    public function onSetup()
    {
        $mockEnvironmentDetector = ehough_mockery_Mockery::mock(tubepress_api_environment_EnvironmentInterface::_);
        $mockWpFunctions         = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('tubepress_api_event_EventDispatcherInterface');

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('booya');
        $mockEnvironmentDetector->shouldReceive('setBaseUrl')->once()->with('booya/plugins/tubepress');

        $this->_sut = new tubepress_addons_wordpress_impl_Callback($mockEnvironmentDetector, $this->_mockEventDispatcher);
    }

    public function testFilter()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.filter.someFilter', ehough_mockery_Mockery::on(array($this, '__callbackFilter')));

        $args = array(1, 'two', array('three'));

        $result = $this->_sut->onFilter('someFilter', $args);

        $this->assertEquals('xyz', $result);
    }

    public function testAction()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.action.someAction', ehough_mockery_Mockery::on(array($this, '__callback')));

        $args = array(1, 'two', array('three'));

        $this->_sut->onAction('someAction', $args);

        $this->assertTrue(true);
    }

    public function testPluginActivation()
    {
        $mock = $this->createMockSingletonService('wordpress.pluginActivator');

        $mock->shouldReceive('execute')->once();

        $this->_sut->onPluginActivation();

        $this->assertTrue(true);
    }

    public function __callbackFilter($event)
    {
        $ok = $event instanceof tubepress_api_event_EventInterface
        && $event->getSubject() === 1 && $event->getArguments() == array('args' => array('two', array('three')));

        $event->setSubject('xyz');

        return $ok;
    }

    public function __callback($event)
    {
        return $event instanceof tubepress_api_event_EventInterface
        && $event->getSubject() === array(1, 'two', array('three'));
    }
}