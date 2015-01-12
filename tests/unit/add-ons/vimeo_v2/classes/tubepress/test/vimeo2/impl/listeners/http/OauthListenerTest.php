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
 * @covers tubepress_vimeo2_impl_listeners_http_OauthListener
 */
class tubepress_test_vimeo2_impl_listeners_http_OauthListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo2_impl_listeners_http_OauthListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOauthClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecContext;

    public function onSetup()
    {
        $this->_mockOauthClient = $this->mock(tubepress_lib_api_http_oauth_v1_ClientInterface::_);
        $this->_mockExecContext = $this->mock(tubepress_app_api_options_ContextInterface::_);


        $this->_sut = new tubepress_vimeo2_impl_listeners_http_OauthListener(

            $this->_mockOauthClient, $this->_mockExecContext
        );
    }

    public function testVimeoUrl()
    {
        $mockHttpRequest = $this->mock('tubepress_lib_api_http_message_RequestInterface');
        $mockUrl         = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockEvent       = $this->mock('tubepress_lib_api_event_EventInterface');

        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockHttpRequest);
        $mockHttpRequest->shouldReceive('getUrl')->once()->andReturn($mockUrl);
        $mockUrl->shouldReceive('getHost')->once()->andReturn('vimeo.com');

        $this->_mockExecContext->shouldReceive('get')->once()->with(tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY)->andReturn('id');
        $this->_mockExecContext->shouldReceive('get')->once()->with(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET)->andReturn('secret');

        $this->_mockOauthClient->shouldReceive('signRequest')->once()->with($mockHttpRequest, ehough_mockery_Mockery::on(array($this, '__callbackVerifyCredentials')));

        $this->_sut->onRequest($mockEvent);

        $this->assertTrue(true);
    }

    public function testNonVimeoUrl()
    {
        $mockHttpRequest = $this->mock('tubepress_lib_api_http_message_RequestInterface');
        $mockUrl         = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockEvent       = $this->mock('tubepress_lib_api_event_EventInterface');

        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockHttpRequest);
        $mockHttpRequest->shouldReceive('getUrl')->once()->andReturn($mockUrl);
        $mockUrl->shouldReceive('getHost')->once()->andReturn('youtube.com');

        $this->_sut->onRequest($mockEvent);

        $this->assertTrue(true);
    }

    public function __callbackVerifyCredentials($credentials)
    {
        return $credentials instanceof tubepress_lib_api_http_oauth_v1_Credentials && $credentials->getIdentifier() === 'id'
            && $credentials->getSecret() === 'secret';
    }
}

