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
 * @covers tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_WidgetInitListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMessageService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTranslator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockMessageService           = $this->mock(tubepress_api_translation_TranslatorInterface::_);
        $this->_mockEventDispatcher          = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockTranslator               = $this->mock(tubepress_api_translation_TranslatorInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockTranslator,
            $this->_mockEventDispatcher
        );
    }

    public function testWidgetInitAction()
    {

        $widgetOps = array('classname' => 'widget_tubepress', 'description' => 'X');

        $this->_mockTranslator->shouldReceive('trans')->once()->with('Displays YouTube or Vimeo videos with TubePress. Limited to a single instance per site. Use the other TubePress widget instead!')->andReturn('X');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_sidebar_widget')->once()->with('tubepress', 'TubePress (legacy)', array($this->_sut, '__fireWidgetHtmlEvent'), $widgetOps);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_widget_control')->once()->with('tubepress', 'TubePress (legacy)', array($this->_sut, '__fireWidgetControlEvent'));
        $this->_mockWordPressFunctionWrapper->shouldReceive('register_widget')->once()->with('tubepress_wordpress_impl_wp_WpWidget');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('hasArgument')->once()->with('unit-testing')->andReturn(true);
        $this->_sut->onAction_widgets_init($mockEvent);
        $this->assertTrue(true);
    }

    public function testPrintWidgetControlHtml()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS);

        $this->_sut->__fireWidgetControlEvent();

        $this->assertTrue(true);
    }

    public function testPrintWidgetHtml()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(1))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML, $mockEvent);

        $this->_sut->__fireWidgetHtmlEvent(array(1));

        $this->assertTrue(true);
    }
}
