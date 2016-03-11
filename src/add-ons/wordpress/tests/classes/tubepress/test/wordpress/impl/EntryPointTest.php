<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_EntryPoint
 */
class tubepress_test_wordpress_impl_EntryPointTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctions;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistence;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var tubepress_wordpress_impl_EntryPoint
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockWpFunctions     = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockPersistence     = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_wordpress_impl_EntryPoint(

            $this->_mockWpFunctions,
            $this->_mockPersistence,
            $this->_mockLogger,
            $this->_mockEventDispatcher,
            array('action1', 'action2'),
            array(
                array('filter1'),
                array('filter2', 20, 4),
                array('filter3', 15)
            )
        );

        $this->_sut->__enableTestMode();
    }

    public function testActivationError()
    {
        $this->_setupDispatchError();

        $this->_sut->callback_onActivation('arg1', 'arg2');
    }

    public function testActivation()
    {
        $this->_setupDispatch(
            tubepress_wordpress_api_Constants::EVENT_PLUGIN_ACTIVATION,
            array('arg1', 'arg2')
        );

        $this->_sut->callback_onActivation('arg1', 'arg2');
    }

    public function testActionError()
    {
        $this->_mockWpFunctions->shouldReceive('current_filter')->once()->andReturn('some-action');

        $this->_setupDispatchError();

        $this->_sut->callback_onAction('arg1', 'arg2');
    }

    public function testActionListener()
    {
        $this->_mockWpFunctions->shouldReceive('current_filter')->once()->andReturn('some-action');

        $eventName = 'tubepress.wordpress.action.some-action';

        $this->_setupDispatch($eventName, array('arg1', 'arg2'));

        $this->_sut->callback_onAction('arg1', 'arg2');
    }

    public function testFilterError()
    {
        $this->_mockWpFunctions->shouldReceive('current_filter')->once()->andReturn('some-filter');

        $this->_setupDispatchError();

        $actual = $this->_sut->callback_onFilter('arg1', 'arg2');

        $this->assertEquals('arg1', $actual);
    }

    public function testFilterListener()
    {
        $this->_mockWpFunctions->shouldReceive('current_filter')->once()->andReturn('some-filter');

        $eventName = 'tubepress.wordpress.filter.some-filter';

        $event = $this->_setupDispatch($eventName, 'arg1', array('args' => array('arg2')));

        $event->shouldReceive('getSubject')->once()->andReturn('hello');

        $actual = $this->_sut->callback_onFilter('arg1', 'arg2');

        $this->assertEquals('hello', $actual);
    }

    public function testShortcodeError()
    {
        $this->_setupDispatchError();

        $actual = $this->_sut->callback_onShortcode('arg1', 'arg2');

        $this->assertEquals('', $actual);
    }

    public function testShortcodeListener()
    {
        $event = $this->_setupDispatch(tubepress_wordpress_api_Constants::EVENT_SHORTCODE_FOUND, array('arg1', 'arg2'));

        $event->shouldReceive('hasArgument')->once()->with('result')->andReturn(true);
        $event->shouldReceive('getArgument')->twice()->with('result')->andReturn('hello');

        $actual = $this->_sut->callback_onShortcode('arg1', 'arg2');

        $this->assertEquals('hello', $actual);
    }

    public function testStart()
    {
        $this->_mockWpFunctions->shouldReceive('load_plugin_textdomain')->once()->with(
            'tubepress',
            false,
            basename(TUBEPRESS_ROOT) . '/src/translations'
        );

        $this->_mockWpFunctions->shouldReceive('add_filter')->once()->with(
            'filter1',
            array($this->_sut, 'callback_onFilter'),
            10, 1
        );

        $this->_mockWpFunctions->shouldReceive('add_filter')->once()->with(
            'filter2',
            array($this->_sut, 'callback_onFilter'),
            20, 4
        );

        $this->_mockWpFunctions->shouldReceive('add_filter')->once()->with(
            'filter3',
            array($this->_sut, 'callback_onFilter'),
            15, 1
        );

        $this->_mockWpFunctions->shouldReceive('add_action')->once()->with(
            'action1',
            array($this->_sut, 'callback_onAction'),
            10, 1
        );

        $this->_mockWpFunctions->shouldReceive('add_action')->once()->with(
            'action2',
            array($this->_sut, 'callback_onAction'),
            10, 1
        );

        $this->_mockWpFunctions->shouldReceive('register_activation_hook')->once()->with(
            basename(TUBEPRESS_ROOT) . '/tubepress.php',
            array($this->_sut, 'callback_onActivation')
        );

        $this->_mockPersistence->shouldReceive('fetch')->once()->with(tubepress_api_options_Names::SHORTCODE_KEYWORD)->andReturn('foobar');

        $this->_mockWpFunctions->shouldReceive('add_shortcode')->once()->with(
            'foobar',
            array($this->_sut, 'callback_onShortcode')
        );

        $this->_sut->start();
    }

    private function _setupDispatchError()
    {
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->andThrow(
            new \RuntimeException('foobar')
        );

        $this->_mockLogger->shouldReceive('error')->once()->with('foobar');
    }

    /**
     * @param      $eventName
     * @param      $subject
     * @param null $args
     *
     * @return \Mockery\MockInterface
     */
    private function _setupDispatch($eventName, $subject, $args = null)
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(
            $subject
        )->andReturn($mockEvent);

        if ($args) {

            $mockEvent->shouldReceive('setArguments')->once()->with($args);
        }

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            $eventName, $mockEvent
        );

        return $mockEvent;
    }
}