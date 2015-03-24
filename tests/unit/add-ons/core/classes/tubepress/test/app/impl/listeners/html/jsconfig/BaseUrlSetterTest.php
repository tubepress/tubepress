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
 * @covers tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter
 */
class tubepress_test_app_impl_listeners_html_jsconfig_BaseUrlSetterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEvent               = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);
        $this->_sut                     = new tubepress_app_impl_listeners_html_jsconfig_BaseUrlSetter(
            $this->_mockEnvironmentDetector
        );
    }

    public function testOnGlobalJsConfig()
    {
        $mockBaseUrl        = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockUserContentUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockAjaxUrl        = $this->mock('tubepress_platform_api_url_UrlInterface');

        $mockBaseUrl->shouldReceive('__toString')->once()->andReturn('mockBaseUrl');
        $mockUserContentUrl->shouldReceive('__toString')->once()->andReturn('mock-user-url');
        $mockAjaxUrl->shouldReceive('__toString')->once()->andReturn('mock-ajax-url');

        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserContentUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getAjaxEndpointUrl')->once()->andReturn($mockAjaxUrl);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(
                'base' => 'mockBaseUrl',
                'usr'  => 'mock-user-url',
                'ajax' => 'mock-ajax-url',
            )
        ));

        $this->_sut->onGlobalJsConfig($this->_mockEvent);

        $this->assertTrue(true);
    }
}