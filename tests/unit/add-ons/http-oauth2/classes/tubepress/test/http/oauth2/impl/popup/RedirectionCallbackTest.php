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
 * @covers tubepress_http_oauth2_impl_popup_RedirectionCallback<extended>
 */
class tubepress_test_http_oauth2_impl_popup_RedirectionCallbackTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_popup_RedirectionCallback
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

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
    private $_mockAccessTokenFetcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockRequestParams      = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockTemplating         = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockAccessTokenFetcher = $this->mock('tubepress_http_oauth2_impl_util_AccessTokenFetcher');
        $this->_mockProvider1          = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);
        $this->_mockProvider2          = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);
        $this->_mockUrlFactory         = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockPersistenceHelper  = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');

        $this->_sut = new tubepress_http_oauth2_impl_popup_RedirectionCallback(
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockUrlFactory,
            $this->_mockPersistenceHelper,
            $this->_mockAccessTokenFetcher
        );
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
        @session_start();
        $_SESSION['tubepress_oauth2_state_provider-2-name'] = 'some state';

        $this->_setProvidersOntoSut();
        $this->_prepRequiredParams();
        $this->_prepPersistenceHelper();

        $mockCurrentUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockQuery      = $this->mock('tubepress_api_url_QueryInterface');
        $mockToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');
        $mockCurrentUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);

        $mockQuery->shouldReceive('get')->once()->with('state')->andReturn('some state');

        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockCurrentUrl);

        $this->_mockProvider2->shouldReceive('isClientSecretUsed')->once()->andReturn(true);
        $this->_mockProvider2->shouldReceive('isStateUsed')->once()->andReturn(true);
        $this->_mockProvider2->shouldReceive('getSlugForToken')->once()->with($mockToken)->andReturn('some slug');

        $this->_mockPersistenceHelper->shouldReceive('saveToken')->once()->with($this->_mockProvider2, 'some slug', $mockToken);

        $this->_mockAccessTokenFetcher->shouldReceive('fetchWithCodeGrant')->once()->with($this->_mockProvider2, 'remote-code')->andReturn($mockToken);

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('oauth2/success-finishAuthorization', array(
            'provider' => $this->_mockProvider2,
            'titleFormat' => 'Successfully connected to %s',
            'slug' => 'some slug',
        ))->andReturn('abc');

        $this->expectOutputString('abc');

        $this->_sut->initiate();
    }

    public function testNoSuchProvider()
    {
        $this->_setProvidersOntoSut();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('provider')->andReturn('provider-33-name');
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('code')->andReturn(true);

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_expectBail('No such OAuth2 provider.');
        $this->_sut->initiate();
    }

    public function testMissingProviderParam()
    {
        $this->_setProvidersOntoSut();

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(false);

        $this->_expectBail('Missing provider parameter.');
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

    private function _prepRequiredParams()
    {
        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('provider')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('provider')->andReturn('provider-2-name');

        $this->_mockProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1-name');
        $this->_mockProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2-name');

        $this->_mockRequestParams->shouldReceive('hasParam')->once()->with('code')->andReturn(true);
        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('code')->andReturn('remote-code');
    }

    private function _prepPersistenceHelper()
    {
        $this->_mockPersistenceHelper->shouldReceive('getClientId')->atLeast(1)->with($this->_mockProvider2)->andReturn('provider-2-clientId');
        $this->_mockPersistenceHelper->shouldReceive('getClientSecret')->atLeast(1)->with($this->_mockProvider2)->andReturn('provider-2-secret');
    }
}
