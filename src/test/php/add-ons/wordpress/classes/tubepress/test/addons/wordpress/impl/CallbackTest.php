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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_addons_wordpress_impl_CallbackTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $mockWpFunctions         = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('tubepress_api_event_EventDispatcherInterface');

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('booya');
        $mockEnvironmentDetector->shouldReceive('setBaseUrl')->once()->with('booya/plugins/tubepress');
    }

    public function testFilter()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.filter.someFilter', ehough_mockery_Mockery::on(array($this, '__callbackFilter')));

        $args = array(1, 'two', array('three'));

        $result = tubepress_addons_wordpress_impl_Callback::onFilter('someFilter', $args);

        $this->assertEquals('xyz', $result);
    }

    public function testAction()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.action.someAction', ehough_mockery_Mockery::on(array($this, '__callback')));

        $args = array(1, 'two', array('three'));

        tubepress_addons_wordpress_impl_Callback::onAction('someAction', $args);

        $this->assertTrue(true);
    }

    public function testPluginActivation()
    {
        $mock = $this->createMockSingletonService('wordpress.pluginActivator');

        $mock->shouldReceive('execute')->once();

        tubepress_addons_wordpress_impl_Callback::onPluginActivation();

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