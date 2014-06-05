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
 * @covers tubepress_core_html_impl_listeners_BaseUrlSetter
 */
class tubepress_test_core_html_impl_listeners_BaseUrlSetterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var tubepress_core_html_impl_listeners_BaseUrlSetter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_sut                     = new tubepress_core_html_impl_listeners_BaseUrlSetter($this->_mockEnvironmentDetector);
    }

    public function testOnJsConfig()
    {
        $mockBaseUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockUserContentUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('foobar');
        $mockUserContentUrl->shouldReceive('toString')->once()->andReturn('barfoo');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserContentUrl);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn(array());
        $event->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(
                'base' => 'foobar',
                'usr'  => 'barfoo'
            )
        ));

        $this->_sut->onJsConfig($event);

        $this->assertTrue(true);
    }
}