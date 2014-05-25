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
 * @covers tubepress_wordpress_impl_actions_WidgetsInit
 */
class tubepress_test_wordpress_impl_actions_WidgetInitTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_actions_WidgetsInit
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWidget;

    public function onSetup()
    {

        $this->_mockMessageService    = $this->mock(tubepress_core_api_translation_TranslatorInterface::_);
        $this->_mockWpFunctionWrapper = $this->mock(tubepress_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockWidget            = $this->mock('tubepress_wordpress_impl_Widget');
        $this->_sut = new tubepress_wordpress_impl_actions_WidgetsInit($this->_mockMessageService, $this->_mockWpFunctionWrapper, $this->_mockWidget);

        $this->_mockMessageService->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });
    }

    public function testInitAction()
    {
        $widgetOps = array('classname' => 'widget_tubepress', 'description' => '<<Displays YouTube or Vimeo videos with TubePress>>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_sidebar_widget')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printWidgetHtml'), $widgetOps);
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_widget_control')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printControlHtml'));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $this->_sut->action($mockEvent);
        $this->assertTrue(true);
    }

    public function testPrintControlHtml()
    {
        $this->_mockWidget->shouldReceive('printControlHtml')->once();

        $this->_sut->printControlHtml();

        $this->assertTrue(true);
    }

    public function testPrintHtml()
    {
        $this->_mockWidget->shouldReceive('printWidgetHtml')->once()->with(array(1));

        $this->_sut->printWidgetHtml(array(1));

        $this->assertTrue(true);
    }
}