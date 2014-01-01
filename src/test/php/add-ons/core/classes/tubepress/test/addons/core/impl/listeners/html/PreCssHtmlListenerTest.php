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
 * @covers tubepress_addons_core_impl_listeners_html_PreCssHtmlListener
 */
class tubepress_test_addons_core_impl_listeners_html_PreCssHtmlListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_html_PreCssHtmlListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHrps;

    public function onSetup()
    {
        $this->_sut      = new tubepress_addons_core_impl_listeners_html_PreCssHtmlListener();
        $this->_mockHrps = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
    }

    public function testPreCssHtmlPage2()
    {
        $mockEvent = new tubepress_spi_event_EventBase('html');

        $this->_mockHrps->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(2);

        $this->_sut->onBeforeCssHtml($mockEvent);

        $result = $mockEvent->getSubject();

        $this->assertEquals('html
<meta name="robots" content="noindex, nofollow" />', $result);
    }

    public function testPreCssHtmlPage1()
    {
        $mockEvent = new tubepress_spi_event_EventBase('html');

        $this->_mockHrps->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_sut->onBeforeCssHtml($mockEvent);

        $result = $mockEvent->getSubject();

        $this->assertEquals('html', $result);
    }
}