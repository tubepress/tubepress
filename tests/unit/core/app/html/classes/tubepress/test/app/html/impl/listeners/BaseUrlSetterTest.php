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
 * @covers tubepress_app_html_impl_listeners_BaseUrlSetter
 */
class tubepress_test_app_html_impl_listeners_BaseUrlSetterTest extends tubepress_test_TubePressUnitTest
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
     * @var tubepress_app_html_impl_listeners_BaseUrlSetter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlUtils;

    public function onSetup()
    {
        $this->_mockEvent               = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);
        $this->_mockUrlUtils            = $this->mock(tubepress_lib_util_api_UrlUtilsInterface::_);
        $this->_sut                     = new tubepress_app_html_impl_listeners_BaseUrlSetter(
            $this->_mockEnvironmentDetector,
            $this->_mockUrlUtils
        );
    }

    public function testOnGlobalJsConfig()
    {

        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUserContentUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserContentUrl);

        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->once()->with($mockBaseUrl)->andReturn('mockBaseUrl');
        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->once()->with($mockUserContentUrl)->andReturn('mock-user-url');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(
                'base' => 'mockBaseUrl',
                'usr'  => 'mock-user-url'
            )
        ));

        $this->_sut->onGlobalJsConfig($this->_mockEvent);

        $this->assertTrue(true);
    }
}