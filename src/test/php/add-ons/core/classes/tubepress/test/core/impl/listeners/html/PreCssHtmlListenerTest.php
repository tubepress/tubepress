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
 * @covers tubepress_core_impl_listeners_html_PreCssHtmlListener
 */
class tubepress_test_core_impl_listeners_html_PreCssHtmlListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_html_PreCssHtmlListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHrps;

    public function onSetup()
    {
        $this->_mockHrps = $this->mock(tubepress_core_api_http_RequestParametersInterface::_);
        $this->_sut      = new tubepress_core_impl_listeners_html_PreCssHtmlListener($this->_mockHrps);
    }

    public function testPreCssHtmlPage2()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_mockHrps->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_api_const_http_ParamName::PAGE, 1)->andReturn(2);

        $mockEvent->shouldReceive('setSubject')->once()->with('html
<meta name="robots" content="noindex, nofollow" />');

        $this->_sut->onBeforeCssHtml($mockEvent);

        $this->assertTrue(true);
    }

    public function testPreCssHtmlPage1()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');

        $this->_mockHrps->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_api_const_http_ParamName::PAGE, 1)->andReturn(1);

        $mockEvent->shouldReceive('setSubject')->once()->with('html');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_sut->onBeforeCssHtml($mockEvent);

        $this->assertTrue(true);
    }
}