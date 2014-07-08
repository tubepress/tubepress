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
 * @covers tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener<extended>
 */
class tubepress_test_app_feature_gallery_impl_listeners_html_NoRobotsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockEvent                       = $this->mock('tubepress_lib_event_api_EventInterface');

        $this->_sut = new tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener(

            $this->_mockHttpRequestParameterService
        );
    }

    public function testOnBeforeCssPage2()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(2);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html
<meta name="robots" content="noindex, nofollow" />');

        $this->_sut->onBeforeCssHtml($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnBeforeCssPage1()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_lib_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(1);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_sut->onBeforeCssHtml($this->_mockEvent);

        $this->assertTrue(true);
    }
}