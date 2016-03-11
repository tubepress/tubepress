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
 * @covers tubepress_wordpress_impl_listeners_wpaction_HeadListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_HeadListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_HeadListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHtmlGenerator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockHtmlGenerator            = $this->mock(tubepress_api_html_HtmlGeneratorInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_HeadListener(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockHtmlGenerator
        );
    }

    public function testWpHeadExecuteInsideAdmin()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(true);

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_sut->onAction_wp_head($mockEvent);
        $this->assertTrue(true);
    }

    public function testWpHeadExecuteOutsideAdmin()
    {

        $this->_mockHtmlGenerator->shouldReceive('getCSS')->once()->andReturn('hello there');
        $this->_mockHtmlGenerator->shouldReceive('getJS')->once()->andReturn('goodbye now');

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->expectOutputString('hello theregoodbye now');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_sut->onAction_wp_head($mockEvent);

        $this->assertTrue(true);
    }
}
