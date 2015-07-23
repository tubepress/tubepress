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
 * @covers tubepress_http_oauth2_impl_RedirectionEndpointCalculator
 */
class tubepress_test_http_oauth2_impl_RedirectionEndpointCalculatorTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_RedirectionEndpointCalculator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockEnvironment     = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_RedirectionEndpointCalculator(
            $this->_mockEnvironment,
            $this->_mockEventDispatcher
        );
    }

    public function testCalculate()
    {
        $mockBaseUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockUrl     = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery   = $this->mock('tubepress_api_url_QueryInterface');
        $mockEvent   = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockUrl);
        $mockUrl->shouldReceive('setPath')->once()->with('/web/php/oauth2');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('provider', 'provider-name');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockUrl, array(
            'providerName' => 'provider-name',
        ))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::OAUTH2_URL_REDIRECTION_ENDPOINT, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockUrl);

        $actual = $this->_sut->getRedirectionEndpoint('provider-name');

        $this->assertSame($mockUrl, $actual);
    }
}
