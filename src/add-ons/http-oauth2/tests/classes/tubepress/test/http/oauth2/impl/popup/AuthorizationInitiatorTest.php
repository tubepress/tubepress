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
 * @covers tubepress_http_oauth2_impl_popup_AuthorizationInitiator<extended>
 */
class tubepress_test_http_oauth2_impl_popup_AuthorizationInitiatorTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_popup_AuthorizationInitiator
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Environment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockProvider1;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockProvider2;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAuthorizationUrl;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAuthorizationQuery;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAccessTokenFetcher;

    public function onSetup()
    {
        $this->_mockRequestParams      = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockOauth2Environment  = $this->mock(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_);
        $this->_mockTemplating         = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockEventDispatcher    = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockProvider1          = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_mockProvider2          = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_mockUrlFactory         = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockPersistenceHelper  = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockAccessTokenFetcher = $this->mock('tubepress_http_oauth2_impl_util_AccessTokenFetcher');

        session_unset();

        $this->_sut = new tubepress_http_oauth2_impl_popup_AuthorizationInitiator(
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockUrlFactory,
            $this->_mockPersistenceHelper,
            $this->_mockAccessTokenFetcher,
            $this->_mockOauth2Environment,
            $this->_mockEventDispatcher
        );
    }

    public function onTearDown()
    {
        session_unset();
    }

    public function testCodeSuccess()
    {
        $this->_setProvidersOntoSut();
        $this->_prepRequiredParams();
        $this->_prepNonceManager();
        $this->_prepClientIdAndSecretSave();

        $this->_prepAuthorizationUrlInitial();
        $this->_prepPersistenceHelper();

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockAuthorizationUrl, array('provider' => $this->_mockProvider2))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockAuthorizationUrl);

        $this->_mockAuthorizationUrl->shouldReceive('__toString')->once()->andReturn('abc');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('oauth2/start', array(
            'provider' => $this->_mockProvider2,
            'titleFormat' => 'Redirecting to %s',
            'url' => $this->_mockAuthorizationUrl,
        ))->andReturn('abc');

        $this->expectOutputString('abc');

        $this->_sut->initiate();
    }

    public function testCodeNonAuthorizationUrlAfterDispatch()
    {
        $this->_setProvidersOntoSut();
        $this->_prepRequiredParams();
        $this->_prepNonceManager();
        $this->_prepClientIdAndSecretSave();

        $this->_prepPersistenceHelper();
        $this->_prepAuthorizationUrlInitial();

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockAuthorizationUrl, array('provider' => $this->_mockProvider2))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $this->_expectBail('Non authorization URL returned.');
        $this->_sut->initiate();
    }

    public function testCodeProviderReturnedBadType()
    {
        $this->_setProvidersOntoSut();
        $this->_prepRequiredParams();
        $this->_prepNonceManager();
        $this->_prepClientIdAndSecretSave();

        $this->_prepPersistenceHelper();

        $this->_mockAuthorizationUrl = $this->mock('tubepress_api_url_UrlInterface');

        $this->_mockProvider2->shouldReceive('isClientSecretUsed')->once()->andReturn(true);
        $this->_mockProvider2->shouldReceive('getAuthorizationGrantType')->once()->andReturn('abc');

        $this->_expectBail('Unsupported authorization grant type.');
        $this->_sut->initiate();
    }

    public function testCodeProviderReturnedNonUrl()
    {
        $this->_setProvidersOntoSut();
        $this->_prepRequiredParams();
        $this->_prepNonceManager();
        $this->_prepClientIdAndSecretSave();

        $this->_prepPersistenceHelper();

        $this->_mockProvider2->shouldReceive('getAuthorizationGrantType')->once()->andReturn('code');
        $this->_mockProvider2->shouldReceive('isClientSecretUsed')->once()->andReturn(true);
        $this->_mockProvider2->shouldReceive('getAuthorizationEndpoint')->once()->andReturn('abc');

        $this->_expectBail('OAuth2 provider returned a non URL.');
        $this->_sut->initiate();
    }

    public function testNoSuchProvider()
    {
        $this->_setProvidersOntoSut();
        $this->_prepNonceManager();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('provider')->andReturn('provider-33-name');
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('csrf_token')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('csrf_token')->andReturn('user-supplied');

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_expectBail('No such OAuth2 provider.');
        $this->_sut->initiate();
    }

    public function testBadNonce()
    {
        $this->_setProvidersOntoSut();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(true);

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('csrf_token')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('csrf_token')->andReturn('hacker-supplied');

        $this->_mockOauth2Environment->shouldReceive('getCsrfSecret')->once()->andReturn('hacker supplied');

        $this->_expectBail('Invalid csrf_token. Possible CSRF attack.');
        $this->_sut->initiate();
    }

    public function testMissingProviderParam()
    {
        $this->_setProvidersOntoSut();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('csrf_token')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(false);

        $this->_expectBail('Missing provider parameter.');
        $this->_sut->initiate();
    }

    public function testMissingNonce()
    {
        $this->_setProvidersOntoSut();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('csrf_token')->andReturn(false);
        $this->_expectBail('Missing csrf_token parameter.');
        $this->_sut->initiate();
    }

    public function testNonOauthProviderPassed()
    {
        $this->setExpectedException('InvalidArgumentException', 'Non tubepress_spi_http_oauth2_Oauth2ProviderInterface in incoming providers.');
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
        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('oauth2/error', array(
            'message' => $message,
        ))->andReturn($message);

        $this->expectOutputString($message);
    }

    private function _setProvidersOntoSut()
    {
        $this->_sut->setOauth2Providers(array(
            $this->_mockProvider1,
            $this->_mockProvider2
        ));
    }

    private function _prepNonceManager()
    {
        $this->_mockOauth2Environment->shouldReceive('getCsrfSecret')->once()->andReturn('user-supplied');
    }

    private function _prepRequiredParams()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('provider')->andReturn('provider-2-name');

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('csrf_token')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('csrf_token')->andReturn('user-supplied');
    }

    private function _prepPersistenceHelper()
    {
        $this->_mockPersistenceHelper->shouldReceive('getClientId')->atLeast(1)->with($this->_mockProvider2)->andReturn('provider-2-clientId');
        $this->_mockPersistenceHelper->shouldReceive('getClientSecret')->atLeast(1)->with($this->_mockProvider2)->andReturn('provider-2-secret');
    }

    private function _prepAuthorizationUrlInitial()
    {
        $this->_mockAuthorizationUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockProvider2->shouldReceive('getAuthorizationEndpoint')->once()->andReturn($this->_mockAuthorizationUrl);

        $this->_mockProvider2->shouldReceive('getAuthorizationGrantType')->once()->andReturn('code');
        $this->_mockProvider2->shouldReceive('isClientSecretUsed')->once()->andReturn(true);


        $this->_mockAuthorizationQuery = $this->mock('tubepress_api_url_QueryInterface');

        $redirectUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $this->_mockOauth2Environment->shouldReceive('getRedirectionUrl')->once()->with($this->_mockProvider2)->andReturn($redirectUrl);
        $redirectUrl->shouldReceive('toString')->once()->andReturn('redirect-uri');

        $this->_mockAuthorizationUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($this->_mockAuthorizationQuery);

        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('client_id', 'provider-2-clientId')->andReturn($this->_mockAuthorizationQuery);
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('response_type', 'code')->andReturn($this->_mockAuthorizationQuery);
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('state', Mockery::type('string'))->andReturn($this->_mockAuthorizationQuery);
        $this->_mockAuthorizationQuery->shouldReceive('set')->once()->with('redirect_uri', 'redirect-uri')->andReturn($this->_mockAuthorizationQuery);

        $this->_mockProvider2->shouldReceive('onAuthorizationUrl')->once()->with($this->_mockAuthorizationUrl, 'provider-2-clientId', 'provider-2-secret');
    }

    private function _prepClientIdAndSecretSave()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('clientId')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('clientSecret')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('clientId')->andReturn('client-id');
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('clientSecret')->andReturn('client-secret');
        $this->_mockPersistenceHelper->shouldReceive('saveClientIdAndSecret')->once()->with($this->_mockProvider2, 'client-id', 'client-secret');
    }
}
