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
 * @covers tubepress_wordpress_impl_http_oauth2_Oauth2UrlProvider
 */
class tubepress_test_wordpress_impl_http_oauth2_Oauth2UrlProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_http_oauth2_Oauth2UrlProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockNonceManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctions;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOauth2Provider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockQuery;

    public function onSetup()
    {
        $this->_mockNonceManager   = $this->mock(tubepress_api_http_NonceManagerInterface::_);
        $this->_mockUrlFactory     = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockWpFunctions    = $this->mock('tubepress_wordpress_impl_wp_WpFunctions');
        $this->_mockOauth2Provider = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);
        $this->_mockQuery          = $this->mock('tubepress_api_url_QueryInterface');

        $this->_sut = new tubepress_wordpress_impl_http_oauth2_Oauth2UrlProvider(

            $this->_mockNonceManager,
            $this->_mockUrlFactory,
            $this->_mockWpFunctions
        );
    }

    public function testOauth2Redirect()
    {
        $url = $this->_setupMockAdminUrl('tubepress_oauth2');

        $actual = $this->_sut->getRedirectionUrl($this->_mockOauth2Provider);

        $this->assertSame($url, $actual);
    }

    public function testOauth2Start()
    {
        $url = $this->_setupMockAdminUrl('tubepress_oauth2_start');

        $this->_mockNonceManager->shouldReceive('getNonce')->once()->andReturn('some-nonce');

        $this->_mockQuery->shouldReceive('set')->once()->with('nonce', 'some-nonce')->andReturn($this->_mockQuery);

        $actual = $this->_sut->getAuthorizationInitiationUrl($this->_mockOauth2Provider);

        $this->assertSame($url, $actual);
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    private function _setupMockAdminUrl($slug)
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($this->_mockQuery);

        $this->_mockWpFunctions->shouldReceive('admin_url')->once()->andReturn('admin-url');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('admin-url')->andReturn($mockUrl);

        $this->_mockOauth2Provider->shouldReceive('getName')->once()->andReturn('provider-name');

        $this->_mockQuery->shouldReceive('set')->once()->with('page', $slug)->andReturn($this->_mockQuery);
        $this->_mockQuery->shouldReceive('set')->once()->with('provider', 'provider-name')->andReturn($this->_mockQuery);

        return $mockUrl;
    }
}