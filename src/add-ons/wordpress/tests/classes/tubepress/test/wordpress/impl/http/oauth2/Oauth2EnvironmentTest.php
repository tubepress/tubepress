<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_http_oauth2_Oauth2Environment
 */
class tubepress_test_wordpress_impl_http_oauth2_Oauth2EnvironmentTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_http_oauth2_Oauth2Environment
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctions;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Provider;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockQuery;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockUrlFactory      = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockWpFunctions     = $this->mock('tubepress_wordpress_impl_wp_WpFunctions');
        $this->_mockOauth2Provider  = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_mockQuery           = $this->mock('tubepress_api_url_QueryInterface');
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_wordpress_impl_http_oauth2_Oauth2Environment(

            $this->_mockUrlFactory,
            $this->_mockWpFunctions,
            $this->_mockEventDispatcher
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function testOauth2Redirect()
    {
        define('AUTH_KEY', 'foobar');

        $url = $this->_setupMockAdminUrl('tubepress_oauth2');

        $this->_setupDispatch($url);

        $actual = $this->_sut->getRedirectionUrl($this->_mockOauth2Provider);

        $this->assertSame($url, $actual);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOauth2Start()
    {
        define('AUTH_KEY', 'foobar');

        $url = $this->_setupMockAdminUrl('tubepress_oauth2_start');

        $actual = $this->_sut->getAuthorizationInitiationUrl($this->_mockOauth2Provider);

        $this->assertSame($url, $actual);
    }

    /**
     * @return Mockery\MockInterface
     */
    private function _setupMockAdminUrl($slug)
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($this->_mockQuery);

        $this->_mockWpFunctions->shouldReceive('admin_url')->once()->with('admin.php')->andReturn('admin-url');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('admin-url')->andReturn($mockUrl);

        $this->_mockOauth2Provider->shouldReceive('getName')->once()->andReturn('provider-name');

        $this->_mockQuery->shouldReceive('set')->once()->with('page', $slug)->andReturn($this->_mockQuery);
        $this->_mockQuery->shouldReceive('set')->once()->with('provider', 'provider-name')->andReturn($this->_mockQuery);
        $this->_mockQuery->shouldReceive('set')->once()->with('csrf_token', '3858f62230ac3c915f300c664312c63f')->andReturn($this->_mockQuery);

        return $mockUrl;
    }

    private function _setupDispatch(Mockery\MockInterface $mockUrl)
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockUrl, array(
            'provider' => $this->_mockOauth2Provider,
        ))->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_event_Events::OAUTH2_URL_REDIRECTION_ENDPOINT,
            $mockEvent
        );

        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockUrl);
    }
}
