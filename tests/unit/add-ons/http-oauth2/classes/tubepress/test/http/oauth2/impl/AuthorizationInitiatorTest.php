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
 * @covers tubepress_http_oauth2_impl_AuthorizationInitiator
 */
class tubepress_test_http_oauth2_impl_AuthorizationInitiatorTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_AuthorizationInitiator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockNonceManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRedirectCalculator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

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
    private $_mockAuthorizationUrl;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAuthorizationQuery;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRedirectionUrl;

    public function onSetup()
    {
        $this->_mockNonceManager       = $this->mock(tubepress_api_http_NonceManagerInterface::_);
        $this->_mockRequestParams      = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockRedirectCalculator = $this->mock('tubepress_http_oauth2_impl_RedirectionEndpointCalculator');
        $this->_mockTemplating         = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockEventDispatcher    = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockProvider1          = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);
        $this->_mockProvider2          = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_AuthorizationInitiator(
            $this->_mockNonceManager,
            $this->_mockRequestParams,
            $this->_mockRedirectCalculator,
            $this->_mockTemplating,
            $this->_mockEventDispatcher
        );

        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    public function onTearDown()
    {
        if (isset($_SESSION)) {
            unset($_SESSION['tubepress_oauth2_state_provider-1-name']);
            unset($_SESSION['tubepress_oauth2_state_provider-2-name']);
        }
    }

    public function testSuccess()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();
        $this->_prepProviderParam();
        $this->_prepClientId();
        $this->_prepAuthorizationUrlInitial();

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockAuthorizationUrl, array('provider' => $this->_mockProvider2))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockAuthorizationUrl);

        $this->_mockAuthorizationUrl->shouldReceive('__toString')->once()->andReturn('abc');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('oauth2/authorization/success', array(
            'url' => $this->_mockAuthorizationUrl,
            'provider' => $this->_mockProvider2,
        ))->andReturn('abc');

        $this->expectOutputString('abc');

        $this->_sut->initiate();
    }

    public function testNonAuthorizationUrlAfterDispatch()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();
        $this->_prepProviderParam();
        $this->_prepClientId();
        $this->_prepAuthorizationUrlInitial();

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockAuthorizationUrl, array('provider' => $this->_mockProvider2))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_expectBail('Non authorization URL returned.');
        $this->_sut->initiate();
    }

    public function testProviderReturnedBadType()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();
        $this->_prepProviderParam();
        $this->_prepClientId();

        $this->_mockAuthorizationUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockProvider2->shouldReceive('getAuthorizationEndpoint')->once()->andReturn($this->_mockAuthorizationUrl);

        $this->_mockProvider2->shouldReceive('getAuthorizationGrantType')->once()->andReturn('abc');

        $this->_expectBail('Unsupported authorization grant type.');
        $this->_sut->initiate();
    }

    public function testProviderReturnedNonUrl()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();
        $this->_prepProviderParam();
        $this->_prepClientId();

        $this->_mockProvider2->shouldReceive('getAuthorizationEndpoint')->once()->andReturn('abc');

        $this->_expectBail('OAuth2 provider returned a non URL.');
        $this->_sut->initiate();
    }

    public function testMissingClientIdParam()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();
        $this->_prepProviderParam();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_clientId')->andReturn(false);

        $this->_expectBail('Missing tubepress_oauth2_clientId parameter.');
        $this->_sut->initiate();
    }

    public function testNoSuchProvider()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_oauth2_provider')->andReturn('abc');

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_expectBail('No such OAuth2 provider.');
        $this->_sut->initiate();
    }

    public function testMissingProviderParam()
    {
        $this->_prepOauth2Providers();
        $this->_prepNonceManager();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_provider')->andReturn(false);

        $this->_expectBail('Missing tubepress_oauth2_provider parameter.');
        $this->_sut->initiate();
    }

    public function testBadNonce()
    {
        $this->_prepOauth2Providers();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_nonce')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_oauth2_nonce')->andReturn('hacker-supplied');

        $this->_mockNonceManager->shouldReceive('isNonceValid')->once()->with('hacker-supplied')->andReturn(false);

        $this->_expectBail('Invalid nonce.');
        $this->_sut->initiate();
    }

    public function testMissingNonce()
    {
        $this->_prepOauth2Providers();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_nonce')->andReturn(false);

        $this->_expectBail('Missing tubepress_oauth2_nonce parameter.');
        $this->_sut->initiate();
    }

    public function testWrongHttpMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->_prepOauth2Providers();

        $this->_expectBail('HTTP POST method required.');
        $this->_sut->initiate();
    }

    public function testNonOauthProviderPassed()
    {
        $this->setExpectedException('InvalidArgumentException', 'Non tubepress_spi_http_oauth_v2_Oauth2ProviderInterface in incoming providers.');
        $this->_sut->setOauth2Providers(array('hi'));
    }

    public function testInitiateNoProviders()
    {
        $this->_expectBail('No OAuth2 providers available.');
        $this->_sut->setOauth2Providers(array());
        $this->_sut->initiate();
    }

    public function testInitiateNullProviders()
    {
        $this->_expectBail('No OAuth2 providers available.');
        $this->_sut->initiate();
    }

    private function _expectBail($message)
    {
        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('oauth2/authorization/error', array(
            'message' => $message,
        ))->andReturn($message);

        $this->expectOutputString($message);
    }

    private function _prepOauth2Providers()
    {
        $this->_sut->setOauth2Providers(array(
            $this->_mockProvider1,
            $this->_mockProvider2
        ));
    }

    private function _prepNonceManager()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_nonce')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_oauth2_nonce')->andReturn('user-supplied');

        $this->_mockNonceManager->shouldReceive('isNonceValid')->once()->with('user-supplied')->andReturn(true);
    }

    private function _prepProviderParam()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_oauth2_provider')->andReturn('provider-2-name');

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');
    }

    private function _prepClientId()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('tubepress_oauth2_clientId')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_oauth2_clientId')->andReturn('client-id');
    }

    private function _prepAuthorizationUrlInitial()
    {
        $this->_mockAuthorizationUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockProvider2->shouldReceive('getAuthorizationEndpoint')->once()->andReturn($this->_mockAuthorizationUrl);

        $this->_mockProvider2->shouldReceive('getAuthorizationGrantType')->once()->andReturn('code');

        $this->_mockAuthorizationQuery = $this->mock('tubepress_api_url_QueryInterface');

        $redirectUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $this->_mockRedirectCalculator->shouldReceive('getRedirectionEndpoint')->once()->with('provider-2-name')->andReturn($redirectUrl);
        $redirectUrl->shouldReceive('toString')->once()->andReturn('redirect-uri');

        $this->_mockAuthorizationUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($this->_mockAuthorizationQuery);

        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('grant_type', 'code');
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('client_id', 'client-id');
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('state', ehough_mockery_Mockery::type('string'));
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('redirect_uri', 'redirect-uri');

        $this->_mockProvider2->shouldReceive('onAuthorizationUrl')->once()->with($this->_mockAuthorizationUrl);
    }
}
