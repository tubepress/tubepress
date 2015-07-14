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
 * @covers tubepress_wordpress_impl_Callback
 */
class tubepress_test_wordpress_impl_CallbackTest extends tubepress_api_test_TubePressUnitTest
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHtmlGenerator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    public function onSetup()
    {
        $this->_mockEventDispatcher  = $this->mock('tubepress_api_event_EventDispatcherInterface');
        $this->_mockActivationHook   = $this->mock('tubepress_wordpress_impl_wp_ActivationHook');
        $this->_mockHtmlGenerator    = $this->mock(tubepress_api_html_HtmlGeneratorInterface::_);
        $this->_mockContext          = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_api_options_ReferenceInterface::_);

        $this->_sut = new tubepress_wordpress_impl_Callback(

            $this->_mockEventDispatcher,
            $this->_mockContext,
            $this->_mockHtmlGenerator,
            $this->_mockOptionsReference,
            $this->_mockActivationHook
        );
    }

    public function testNonArrayIncoming()
    {
        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('foO'));

        $this->_mockHtmlGenerator->shouldReceive('getHtml')->once()->andReturn('html for shortcode');

        $this->_mockContext->shouldReceive('setEphemeralOptions')->twice()->with(array());
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SHORTCODE_KEYWORD)->andReturn('sc');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::on(function ($shortcode) {

            return $shortcode instanceof tubepress_api_shortcode_ShortcodeInterface && $shortcode->getName() === 'sc'
                && $shortcode->getInnerContent() === 'shortcode content' && $shortcode->getAttributes() === array();

        }))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_wordpress_api_Constants::SHORTCODE_PARSED, $mockEvent);

        $result = $this->_sut->onShortcode('', 'shortcode content');

        $this->assertEquals('html for shortcode', $result);
    }

    public function testShortcode()
    {
        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('foO'));

        $this->_mockHtmlGenerator->shouldReceive('getHtml')->once()->andReturn('html for shortcode');

        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with(array('foO' => 'bar'));
        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with(array());
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SHORTCODE_KEYWORD)->andReturn('sc');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::on(function ($shortcode) {

            return $shortcode instanceof tubepress_api_shortcode_ShortcodeInterface && $shortcode->getName() === 'sc'
            && $shortcode->getInnerContent() === 'shortcode content' && $shortcode->getAttributes() === array('foO' => 'bar');

        }))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_wordpress_api_Constants::SHORTCODE_PARSED, $mockEvent);

        $options = array('foo' => 'bar');

        $result = $this->_sut->onShortcode($options, 'shortcode content');

        $this->assertEquals('html for shortcode', $result);
    }

    public function testFilter()
    {
        $args = array(1, 'two', array('three'));

        $mockFilterEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockFilterEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(1, array('args' => array('two', array('three'))))->andReturn($mockFilterEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with('tubepress.wordpress.filter.someFilter', $mockFilterEvent);

        $result = $this->_sut->onFilter('someFilter', $args);

        $this->assertEquals('abc', $result);
    }

    public function testAction()
    {
        $mockActionEvent = $this->mock('tubepress_api_event_EventInterface');

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
        return $event instanceof tubepress_api_event_EventInterface
        && $event->getSubject() === array(1, 'two', array('three'));
    }
}