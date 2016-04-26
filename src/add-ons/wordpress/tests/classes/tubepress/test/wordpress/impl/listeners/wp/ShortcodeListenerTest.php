<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wp_ShortcodeListener
 */
class tubepress_test_wordpress_impl_listeners_wp_ShortcodeListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var tubepress_wordpress_impl_listeners_wp_ShortcodeListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHtmlGenerator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockIncomingEvent;

    public function onSetup()
    {
        $this->_mockEventDispatcher  = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockHtmlGenerator    = $this->mock(tubepress_api_html_HtmlGeneratorInterface::_);
        $this->_mockContext          = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockIncomingEvent    = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_wordpress_impl_listeners_wp_ShortcodeListener(

            $this->_mockEventDispatcher,
            $this->_mockContext,
            $this->_mockHtmlGenerator,
            $this->_mockOptionsReference,
            $this->_mockLogger
        );
    }

    /**
     * @dataProvider getDataShortcode
     */
    public function testShortcode($incomingAttributes, $outgoingAttributes)
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockOptionsReference->shouldReceive('getAllOptionNames')->once()->andReturn(array('foO'));

        $this->_mockHtmlGenerator->shouldReceive('getHtml')->once()->andReturn('html for shortcode');

        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with($outgoingAttributes);
        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with(array());
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SHORTCODE_KEYWORD)->andReturn('sc');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(Mockery::on(function ($shortcode) use ($outgoingAttributes) {

            return $shortcode instanceof tubepress_api_shortcode_ShortcodeInterface && $shortcode->getName() === 'sc'
            && $shortcode->getInnerContent() === 'shortcode content' && $shortcode->getAttributes() === $outgoingAttributes;

        }))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_wordpress_api_Constants::SHORTCODE_PARSED, $mockEvent);

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array(
            $incomingAttributes, 'shortcode content',
        ));

        $this->_mockIncomingEvent->shouldReceive('setArgument')->once()->with('result', 'html for shortcode');

        $this->_sut->onShortcode($this->_mockIncomingEvent);
    }

    public function getDataShortcode()
    {
        return array(

            array('', array()),
            array(array('foo' => 'bar'), array('foO' => 'bar')),
        );
    }
}
