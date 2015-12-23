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
 * @covers tubepress_http_oauth2_impl_listeners_Oauth2Listener<extended>
 */
class tubepress_test_http_oauth2_impl_listeners_Oauth2ListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_listeners_Oauth2Listener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAccessTokenFetcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider2;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequest;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrl;

    public function onSetup()
    {
        $this->_mockLogger             = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockPersistence        = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockPersistenceHelper  = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockAccessTokenFetcher = $this->mock('tubepress_http_oauth2_impl_util_AccessTokenFetcher');
        $this->_mockHttpRequest        = $this->mock('tubepress_api_http_message_RequestInterface');
        $this->_mockEvent              = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockProvider1          = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_mockProvider2          = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_mockUrl                = $this->mock(tubepress_api_url_UrlInterface::_);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockHttpRequest);
        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_http_oauth2_impl_listeners_Oauth2Listener(
            $this->_mockLogger,
            $this->_mockPersistence,
            $this->_mockPersistenceHelper,
            $this->_mockAccessTokenFetcher
        );

        $this->_sut->setOauth2Providers(array(
            $this->_mockProvider1,
            $this->_mockProvider2,
        ));
    }

    public function testCannotRefreshToken()
    {
        $this->_setupRemoteApiCall(true);
        $this->_setupRequestBasics();
        $this->_setupProviderBasics();

        $this->_mockProvider1->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);
        $this->_mockProvider2->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(true);

        $mockToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockNewToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockToken->shouldReceive('isExpired')->once()->andReturn(true);
        $mockToken->shouldReceive('getRefreshToken')->once()->andReturn('refresh-token');

        $this->_mockAccessTokenFetcher->shouldReceive('fetchWithRefreshToken')->once()->with($this->_mockProvider2, $mockToken)->andReturn(null);

        $this->_mockPersistenceHelper->shouldReceive('getStoredToken')->once()->with($this->_mockProvider2)->andReturn($mockToken);

        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testRefreshToken()
    {
        $this->_setupRemoteApiCall(true);
        $this->_setupRequestBasics();
        $this->_setupProviderBasics();

        $this->_mockProvider1->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);
        $this->_mockProvider2->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(true);

        $mockToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockNewToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockToken->shouldReceive('isExpired')->once()->andReturn(true);
        $mockToken->shouldReceive('getRefreshToken')->once()->andReturn('refresh-token');

        $this->_mockAccessTokenFetcher->shouldReceive('fetchWithRefreshToken')->once()->with($this->_mockProvider2, $mockToken)->andReturn($mockNewToken);

        $this->_mockPersistenceHelper->shouldReceive('getStoredToken')->once()->with($this->_mockProvider2)->andReturn($mockToken);
        $this->_mockPersistenceHelper->shouldReceive('updateToken')->once()->with($mockToken, $mockNewToken);
        $this->_mockPersistenceHelper->shouldReceive('getClientId')->once()->with($this->_mockProvider2)->andReturn('client-id');
        $this->_mockPersistenceHelper->shouldReceive('getClientSecret')->once()->with($this->_mockProvider2)->andReturn('client-secret');

        $this->_mockProvider2->shouldReceive('authorizeRequest')->once()->with($this->_mockHttpRequest, $mockNewToken, 'client-id', 'client-secret');
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testNoRefreshToken()
    {
        $this->_setupRemoteApiCall(true);
        $this->_setupRequestBasics();
        $this->_setupProviderBasics();

        $this->_mockProvider1->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);
        $this->_mockProvider2->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(true);

        $mockToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockToken->shouldReceive('isExpired')->once()->andReturn(true);
        $mockToken->shouldReceive('getRefreshToken')->once()->andReturn(null);

        $this->_mockPersistenceHelper->shouldReceive('getStoredToken')->once()->with($this->_mockProvider2)->andReturn($mockToken);

        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testNoToken()
    {
        $this->_setupRemoteApiCall(true);
        $this->_setupRequestBasics();
        $this->_setupProviderBasics();

        $this->_mockProvider1->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);
        $this->_mockProvider2->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(true);

        $this->_mockPersistenceHelper->shouldReceive('getStoredToken')->once()->with($this->_mockProvider2)->andReturnNull();

        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testNoTakers()
    {
        $this->_setupRemoteApiCall(true);
        $this->_setupRequestBasics();
        $this->_setupProviderBasics();

        $this->_mockProvider1->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);
        $this->_mockProvider2->shouldReceive('wantsToAuthorizeRequest')->once()->with($this->_mockHttpRequest)->andReturn(false);


        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testNonApiRequest2()
    {
        $this->_setupRemoteApiCall(false);

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    public function testNonApiRequest1()
    {
        $this->_mockHttpRequest->shouldReceive('getConfig')->atLeast(1)->andReturn(array(
            'foo' => 'bar'
        ));

        $this->_sut->onHttpRequest($this->_mockEvent);
    }

    private function _setupRemoteApiCall($true)
    {
        $this->_mockHttpRequest->shouldReceive('getConfig')->atLeast(1)->andReturn(array(
            'tubepress-remote-api-call' => $true
        ));
    }

    private function _setupProviderBasics()
    {
        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');
    }

    private function _setupRequestBasics()
    {
        $this->_mockHttpRequest->shouldReceive('getMethod')->atLeast(1)->andReturn('GET');
        $this->_mockHttpRequest->shouldReceive('getUrl')->atLeast(1)->andReturn($this->_mockUrl);

        $this->_mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('mock-url');
    }
}
