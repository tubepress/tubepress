<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener
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
        $this->_mockOauthClient = $this->createMockSingletonService('ehough_coauthor_api_v1_ClientInterface');
        $this->_mockExecContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);


        $this->_sut = new tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener($this->_mockOauthClient, $this->_mockExecContext);
    }

    public function testVimeoUrl()
    {
        $mockHttpRequest = new ehough_shortstop_api_HttpRequest('POST', 'https://vimeo.com/a/b/c.html');
        $mockEvent       = new ehough_tickertape_GenericEvent($mockHttpRequest);

        $this->_mockExecContext->shouldReceive('get')->once()->with(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY)->andReturn('id');
        $this->_mockExecContext->shouldReceive('get')->once()->with(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET)->andReturn('secret');

        $this->_mockOauthClient->shouldReceive('signRequest')->once()->with($mockHttpRequest, ehough_mockery_Mockery::on(array($this, '__callbackVerifyCredentials')));

        $this->_sut->onRequest($mockEvent);

        $this->assertTrue(true);
    }

    public function testNonVimeoUrl()
    {
        $mockHttpRequest = new ehough_shortstop_api_HttpRequest('POST', 'http://youtube.com/a/b/c.html');
        $mockEvent       = new ehough_tickertape_GenericEvent($mockHttpRequest);

        $this->_sut->onRequest($mockEvent);

        $this->assertTrue(true);
    }

    public function __callbackVerifyCredentials($credentials)
    {
        return $credentials instanceof ehough_coauthor_api_v1_Credentials && $credentials->getIdentifier() === 'id'
            && $credentials->getSecret() === 'secret';
    }
}

