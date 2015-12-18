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
 * @covers tubepress_vimeo3_impl_oauth_VimeoOauth2Provider
 */
class tubepress_test_vimeo3_impl_oauth_VimeoOauth2ProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_oauth_VimeoOauth2Provider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockUrlFactory  = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockStringUtils = $this->mock(tubepress_api_util_StringUtilsInterface::_);

        $this->_sut = new tubepress_vimeo3_impl_oauth_VimeoOauth2Provider(
            $this->_mockUrlFactory,
            $this->_mockStringUtils
        );
    }

    /**
     * @dataProvider getDataSlug
     */
    public function testGetSlug($expected, array $extraParams)
    {
        $mockToken = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');

        $mockToken->shouldReceive('getExtraParams')->once()->andReturn($extraParams);

        if (!isset($extraParams['user'])) {

            $mockToken->shouldReceive('getAccessToken')->once()->andReturn('access-token');
        }

        $actual = $this->_sut->getSlugForToken($mockToken);

        $this->assertEquals($expected, $actual);
    }

    public function getDataSlug()
    {
        return array(

            array('Eric Hough', array(
                'user' => array(
                    'name' => 'Eric Hough'
                )
            )),
            array('Eric Hough', array(
                'user' => array(
                    'name' => 'Eric Hough'
                ),
                'scope' => 'foobar'
            )),
            array('Eric Hough (All Access)', array(
                'user' => array(
                    'name' => 'Eric Hough'
                ),
                'scope' => 'foobar private something'
            )),
            array('Vimeo-82ad97e3b88a2d49ac02f3eb4f5a808d', array()),
        );
    }

    /**
     * @dataProvider getDataWantsToAuthorizeRequest
     */
    public function testWantsToAuthorizeRequest($stringUrl, $expected)
    {
        $urlFactory  = new tubepress_url_impl_puzzle_UrlFactory();
        $mockUrl     = $urlFactory->fromString($stringUrl);
        $mockRequest = $this->mock('tubepress_api_http_message_RequestInterface');

        if ($mockUrl->getHost() === 'api.vimeo.com') {

            $this->_mockStringUtils->shouldReceive('startsWith')->once()->with($mockUrl->getPath(), '/oauth')->andReturnUsing(
                array(new tubepress_util_impl_StringUtils(), 'startsWith')
            );
        }

        $mockRequest->shouldReceive('getUrl')->once()->andReturn($mockUrl);

        $actual = $this->_sut->wantsToAuthorizeRequest($mockRequest);

        $this->assertEquals($expected, $actual);
    }

    public function getDataWantsToAuthorizeRequest()
    {
        return array(

            array('http://foo.com', false),
            array('http://vimeo.com', false),
            array('http://api.vimeo.com/oauth/something', false),
            array('http://api.vimeo.com/oauth', false),
            array('http://api.vimeo.com/foo', true),
        );
    }

    public function testOnAccessTokenRequest()
    {
        $mockRequest = $this->mock('tubepress_api_http_message_RequestInterface');

        $mockRequest->shouldReceive('setHeader')->once()->with('Authorization', 'basic ' . base64_encode('client-id:client-secret'));

        $this->_sut->onAccessTokenRequest(
            $mockRequest,
            'client-id',
            'client-secret'
        );
    }

    public function testAuthorizeRequest()
    {
        $mockRequest = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockToken   = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');

        $mockToken->shouldReceive('getAccessToken')->once()->andReturn('access-token');

        $mockRequest->shouldReceive('setHeader')->once()->with('Authorization', 'bearer access-token');
        $mockRequest->shouldReceive('setHeader')->once()->with('Accept', 'application/vnd.vimeo.*+json;version=3.2');

        $this->_sut->authorizeRequest(
            $mockRequest,
            $mockToken,
            'client-id',
            'client-secret'
        );
    }

    public function testAccessTokenEndpoint()
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://api.vimeo.com/oauth/access_token')->andReturn($mockUrl);

        $actual = $this->_sut->getTokenEndpoint();

        $this->assertSame($mockUrl, $actual);
    }

    public function testAuthorizationEndpoint()
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://api.vimeo.com/oauth/authorize')->andReturn($mockUrl);

        $actual = $this->_sut->getAuthorizationEndpoint();

        $this->assertSame($mockUrl, $actual);
    }

    public function testBasics()
    {
        $this->assertInstanceOf('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider', $this->_sut);
        $this->assertEquals('vimeoV3', $this->_sut->getName());
        $this->assertEquals('bearer', $this->_sut->getAccessTokenType());
        $this->assertEquals('code', $this->_sut->getAuthorizationGrantType());
        $this->assertEquals('Vimeo', $this->_sut->getDisplayName());
        $this->assertTrue($this->_sut->isStateUsed());
        $this->assertTrue($this->_sut->isClientSecretUsed());
    }

    public function testTranslatedStuff()
    {
        $mockTranslator = $this->mock(tubepress_api_translation_TranslatorInterface::_);

        $mockTranslator->shouldReceive('trans')->atLeast(1)->andReturnUsing(function ($incoming) {

            return "<<< $incoming >>>";
        });

        $this->assertEquals('<<< Client Identifier >>>', $this->_sut->getTranslatedTermForClientId($mockTranslator));
        $this->assertEquals('<<< Client Secret >>>', $this->_sut->getTranslatedTermForClientSecret($mockTranslator));
        $this->assertEquals('<<< App Callback URL >>>', $this->_sut->getTranslatedTermForRedirectEndpoint($mockTranslator));
        $this->assertEquals(array(
            '<<< <a href="%client-registration-url%" target="_blank">Click here</a> to "Create a new app" with Vimeo >>>',
            '<<< Use anything you\'d like for the App Name, App Description, and App URL >>>',
        ), $this->_sut->getTranslatedClientRegistrationInstructions($mockTranslator));
    }
}