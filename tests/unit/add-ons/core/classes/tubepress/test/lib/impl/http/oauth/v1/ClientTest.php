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
 * @covers tubepress_lib_impl_http_oauth_v1_Client
 */
class tubepress_test_lib_http_impl_oauth_v1_ClientTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_lib_impl_http_oauth_v1_Client
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_lib_impl_http_oauth_v1_Client();
    }

    public function testSignHttp65()
    {
        $request = $this->_createMockRequest(

            'POST',
            'http://foo.bar:65/some/thing.php?test=false#abc'
        );

        $request->shouldReceive('setHeader')->once()->with('Authorization', ehough_mockery_Mockery::on(function ($value) {

            $regex = '~^OAuth oauth_consumer_key="id", oauth_nonce="[0-9a-f]{32}", oauth_signature_method="HMAC-SHA1", oauth_timestamp="[0-9]{10}", oauth_version="1\.0", oauth_signature="[0-9-a-zA-Z=%]+"$~';

            return preg_match_all($regex, $value, $matches) === 1;
        }));
        $this->_sut->signRequest($request, $this->_mockCredentials());

        $this->assertTrue(true);
    }

    public function testSignHttp80()
    {
        $request = $this->_createMockRequest(

            'POST',
            'http://foo.bar/some/thing.php?test=false#abc'
        );

        $request->shouldReceive('setHeader')->once()->with('Authorization', ehough_mockery_Mockery::on(function ($value) {

            $regex = '~^OAuth oauth_consumer_key="id", oauth_nonce="[0-9a-f]{32}", oauth_signature_method="HMAC-SHA1", oauth_timestamp="[0-9]{10}", oauth_version="1\.0", oauth_signature="[0-9-a-zA-Z=%]+"$~';

            return preg_match_all($regex, $value, $matches) === 1;
        }));

        $this->_sut->signRequest($request, $this->_mockCredentials());

        $this->assertTrue(true);
    }

    private function _mockCredentials()
    {
        return new tubepress_lib_api_http_oauth_v1_Credentials('id', 'secret');
    }

    private function _createMockRequest($method, $url)
    {
        $urlFactory = new tubepress_url_impl_puzzle_UrlFactory();
        $realUrl    = $urlFactory->fromString($url);

        $mockRequest = $this->mock('tubepress_lib_api_http_message_RequestInterface');
        $mockUrl     = $this->mock('tubepress_platform_api_url_UrlInterface');
        $mockQuery   = $this->mock('tubepress_platform_api_url_QueryInterface');

        $mockRequest->shouldReceive('getUrl')->atLeast(1)->andReturn($mockUrl);
        $mockRequest->shouldReceive('getMethod')->atLeast(1)->andReturn($method);
        $mockUrl->shouldReceive('getQuery')->atLeast(1)->andReturn($mockQuery);
        $mockUrl->shouldReceive('getScheme')->atLeast(1)->andReturn($realUrl->getScheme());
        $mockUrl->shouldReceive('getPort')->atLeast(1)->andReturn($realUrl->getPort());
        $mockUrl->shouldReceive('getAuthority')->atLeast(1)->andReturn($realUrl->getAuthority());
        $mockUrl->shouldReceive('getPath')->atLeast(1)->andReturn($realUrl->getPath());
        $mockUrl->shouldReceive('getHost')->andReturn($realUrl->getHost());
        $mockQuery->shouldReceive('toArray')->once()->andReturn($realUrl->getQuery()->toArray());

        return $mockRequest;
    }
}