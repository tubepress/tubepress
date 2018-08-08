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
 * @covers tubepress_vimeo3_impl_oauth_VimeoOauth2Provider
 */
class tubepress_test_vimeo3_impl_oauth_VimeoOauth2ProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_oauth_VimeoOauth2Provider
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var Mockery\MockInterface
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

        $actual = $this->_sut->getSlugForToken($mockToken);

        $this->assertEquals($expected, $actual);
    }

    public function getDataSlug()
    {
        return array(

            array('Eric Hough', array(
                'user' => array(
                    'name' => 'Eric Hough',
                ),
            )),
            array('Eric Hough', array(
                'user' => array(
                    'name' => 'Eric Hough',
                ),
                'scope' => 'foobar',
            )),
            array('Eric Hough (All Access)', array(
                'user' => array(
                    'name' => 'Eric Hough',
                ),
                'scope' => 'foobar private something',
            )),
            array('Basic access (public videos only)', array()),
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

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://api.vimeo.com/oauth/authorize/client')->andReturn($mockUrl);

        $actual = $this->_sut->getTokenEndpoint();

        $this->assertSame($mockUrl, $actual);
    }

    public function testAuthorizationEndpoint()
    {
        $actual = $this->_sut->getAuthorizationEndpoint();

        $this->assertNull($actual);
    }

    public function testBasics()
    {
        $this->assertInstanceOf('tubepress_vimeo3_impl_oauth_VimeoOauth2Provider', $this->_sut);
        $this->assertEquals('vimeoV3', $this->_sut->getName());
        $this->assertEquals('bearer', $this->_sut->getAccessTokenType());
        $this->assertEquals('client_credentials', $this->_sut->getAuthorizationGrantType());
        $this->assertEquals('Vimeo', $this->_sut->getDisplayName());
        $this->assertFalse($this->_sut->isStateUsed());
        $this->assertTrue($this->_sut->isClientSecretUsed());
        $this->assertEquals('OAuth2 Client Identifier', $this->_sut->getUntranslatedTermForClientId());
        $this->assertEquals('OAuth2 Client Secret', $this->_sut->getUntranslatedTermForClientSecret());
    }

    public function testTranslatedStuff()
    {
        $mockTranslator  = $this->mock(tubepress_api_translation_TranslatorInterface::_);
        $mockRedirectUrl = $this->mock(tubepress_api_url_UrlInterface::_);

        $mockRedirectUrl->shouldReceive('toString')->once()->andReturn('redirect url as string');

        $mockTranslator->shouldReceive('trans')->atLeast(1)->andReturnUsing(function ($incoming) {

            return "<<< $incoming >>>";
        });

        $expected = array(

            '<<< <a href="%client-registration-url%" target="_blank">Click here</a> to create a new Vimeo &quot;App&quot;. >>>',
            array(
                '<<< Use anything you\'d like for the App Name, App Description, and App URL. >>>',
                '<<< In the field for &quot;App Callback URLs&quot;, enter:<br /><code>%redirect-uri%</code> >>>',
            ),
            '<<< Under the &quot;OAuth2&quot; tab of your new Vimeo App, you will find your &quot;Client Identifier&quot; and &quot;Client Secret&quot;. Enter those values into the text boxes below. >>>',
            '<<< Click the &quot;New token&quot; button below to authorize TubePress to communicate with Vimeo on your behalf. This step will take place in a popup window. >>>',
        );

        $actual = $this->_sut->getTranslatedClientRegistrationInstructions($mockTranslator, $mockRedirectUrl);

        $this->assertEquals($expected, $actual);
    }
}
