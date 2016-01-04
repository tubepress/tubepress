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
 * @covers tubepress_http_oauth2_impl_util_AccessTokenFetcher
 */
class tubepress_test_http_oauth2_impl_util_AccessTokenFetcherTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_util_AccessTokenFetcher
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRedirectionEndpointCalculator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider;

    public function onSetup()
    {
        $this->_mockHttpClient                    = $this->mock(tubepress_api_http_HttpClientInterface::_);
        $this->_mockPersistenceHelper             = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockRedirectionEndpointCalculator = $this->mock(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_);
        $this->_mockProvider                      = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_util_AccessTokenFetcher(
            $this->_mockHttpClient,
            $this->_mockPersistenceHelper,
            $this->_mockRedirectionEndpointCalculator
        );
    }

    public function testGoodToken()
    {
        $this->_mockProvider->shouldReceive('getAccessTokenType')->atLeast(1)->andReturn('some type');

        $response = $this->_setupResponse();
        $mockBody = $this->mock('tubepress_api_streams_StreamInterface');

        $response->shouldReceive('getStatusCode')->times(1)->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn($mockBody);

        $mockBody->shouldReceive('toString')->once()->andReturn(json_encode(array(
            'access_token'  => 'access token',
            'token_type'    => 'some type',
            'expires_in'    => '123',
            'refresh_token' => 'refresh token',
            'foo'           => 'bar',
        )));

        $actual = $this->_sut->fetchWithCodeGrant($this->_mockProvider, 'the code');
        $this->assertEquals($actual->getAccessToken(), 'access token');
        $this->assertEquals($actual->getRefreshToken(), 'refresh token');
        $this->assertEquals($actual->isExpired(), false);
        $this->assertEquals($actual->getEndOfLifeUnixTime(), time() + 123);
        $this->assertEquals($actual->getExtraParams(), array(
            'token_type' => 'some type',
            'foo'        => 'bar',
        ));

        $this->assertInstanceOf('tubepress_api_http_oauth_v2_TokenInterface', $actual);
    }

    public function testWrongTokenType()
    {
        $this->_mockProvider->shouldReceive('getAccessTokenType')->atLeast(1)->andReturn('expected type');

        $this->setExpectedException('RuntimeException', 'Provider name should have returned a token type of expected type but instead returned x');

        $response = $this->_setupResponse();
        $mockBody = $this->mock('tubepress_api_streams_StreamInterface');

        $response->shouldReceive('getStatusCode')->times(1)->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn($mockBody);

        $mockBody->shouldReceive('toString')->once()->andReturn(json_encode(array(
            'access_token' => 'token',
            'token_type'   => 'x'
        )));

        $this->_sut->fetchWithCodeGrant($this->_mockProvider, 'the code');

        $this->assertTrue(true);
    }

    /**
     * @dataProvider getDataBadTokens
     */
    public function testBadTokens(array $tokenData, $expectedMessage)
    {
        $this->setExpectedException('RuntimeException', $expectedMessage);

        $response = $this->_setupResponse();
        $mockBody = $this->mock('tubepress_api_streams_StreamInterface');

        $response->shouldReceive('getStatusCode')->times(1)->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn($mockBody);

        $this->_mockProvider->shouldReceive('getAccessTokenType')->andReturn('token-type');

        $mockBody->shouldReceive('toString')->once()->andReturn(json_encode($tokenData));

        $this->_sut->fetchWithCodeGrant($this->_mockProvider, 'the code');

        $this->assertTrue(true);
    }

    public function getDataBadTokens()
    {
        return array(
            array(array(), 'Provider name did not return an access token in their response'),
            array(array('a' => 'b'), 'Provider name did not return an access token in their response'),
            array(array('access_token' => 'access_token'), 'Provider name did not return a token type in their response'),
        );
    }

    public function testFetch400()
    {
        $this->setExpectedException('RuntimeException', 'Provider name responded with an HTTP 400: foobar');

        $response = $this->_setupResponse();
        $mockBody = $this->mock('tubepress_api_streams_StreamInterface');

        $response->shouldReceive('getStatusCode')->times(2)->andReturn(400);
        $response->shouldReceive('getBody')->once()->andReturn($mockBody);

        $mockBody->shouldReceive('toString')->once()->andReturn(json_encode(array(
            'error' => 'foobar'
        )));

        $this->_sut->fetchWithCodeGrant($this->_mockProvider, 'the code');

        $this->assertTrue(true);
    }

    public function testFetch500()
    {
        $this->setExpectedException('RuntimeException', 'Provider name responded with an HTTP 500: abc');

        $response = $this->_setupResponse();
        $mockBody = $this->mock('tubepress_api_streams_StreamInterface');

        $response->shouldReceive('getStatusCode')->times(3)->andReturn(500);
        $response->shouldReceive('getBody')->once()->andReturn($mockBody);

        $mockBody->shouldReceive('toString')->once()->andReturn('abc');

        $this->_sut->fetchWithCodeGrant($this->_mockProvider, 'the code');

        $this->assertTrue(true);
    }

    private function _setupResponse()
    {
        $mockTokenUrl    = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockRedirectUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockHttpRequest = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockHttpResp    = $this->mock('tubepress_api_http_message_ResponseInterface');

        $this->_mockProvider->shouldReceive('getTokenEndpoint')->once()->andReturn($mockTokenUrl);
        $this->_mockProvider->shouldReceive('getDisplayName')->andReturn('Provider name');
        $this->_mockProvider->shouldReceive('onAccessTokenRequest')->once()->with($mockHttpRequest, 'client-id', 'client-secret');

        $this->_mockRedirectionEndpointCalculator->shouldReceive('getRedirectionUrl')->once()->with($this->_mockProvider)->andReturn($mockRedirectUrl);
        $this->_mockHttpClient->shouldReceive('createRequest')->once()->with('POST', $mockTokenUrl, array(
            'body' => array(
                'code' => 'the code',
                'grant_type' => 'authorization_code',
                'redirect_uri' => $mockRedirectUrl
            )
        ))->andReturn($mockHttpRequest);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockHttpRequest)->andReturn($mockHttpResp);

        $this->_mockPersistenceHelper->shouldReceive('getClientId')->once()->with($this->_mockProvider)->andReturn('client-id');
        $this->_mockPersistenceHelper->shouldReceive('getClientSecret')->once()->with($this->_mockProvider)->andReturn('client-secret');

        return $mockHttpResp;
    }
}
