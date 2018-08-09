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
 * @covers tubepress_http_oauth2_impl_util_PersistenceHelper
 */
class tubepress_test_http_oauth2_impl_util_PersistenceHelperTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistence;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockArrayReader;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    public function onSetup()
    {
        $this->_mockPersistence = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockArrayReader = $this->mock(tubepress_api_array_ArrayReaderInterface::_);
        $this->_mockContext     = $this->mock(tubepress_api_options_ContextInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_util_PersistenceHelper(
            $this->_mockPersistence,
            $this->_mockArrayReader,
            $this->_mockContext
        );
    }

    public function testSaveToken()
    {
        $mockProvider = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $mockToken    = $this->mock('tubepress_api_http_oauth_v2_TokenInterface');

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::OAUTH2_TOKENS)->andReturn(json_encode(array(

            'provider-1' => array(
                'foo' => array(
                    'access_token' => 'something',
                ),
            ),
        )));

        $mockProvider->shouldReceive('getName')->once()->andReturn('provider-1');

        $mockToken->shouldReceive('getAccessToken')->once()->andReturn('new-access-token');
        $mockToken->shouldReceive('getEndOfLifeUnixTime')->once()->andReturn(time() + 500);
        $mockToken->shouldReceive('getExtraParams')->once()->andReturn(array('hi' => 'there'));
        $mockToken->shouldReceive('getRefreshToken')->atLeast(1)->andReturn('refresh-token');

        $this->_mockPersistence->shouldReceive('queueForSave')->once()->with(
            tubepress_api_options_Names::OAUTH2_TOKENS,
            json_encode(array(

                'provider-1' => array(
                    'foo' => array(
                        'access_token' => 'something',
                    ),
                    'slug' => array(
                        'access_token'  => 'new-access-token',
                        'expiry_unix'   => time() + 500,
                        'extra'         => array('hi' => 'there'),
                        'refresh_token' => 'refresh-token',
                    ),

                ), )));

        $this->_mockPersistence->shouldReceive('flushSaveQueue')->once();

        $this->_sut->saveToken($mockProvider, 'slug', $mockToken);
    }

    public function testGetTokenUseFirst()
    {
        $mockProvider = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $mockProvider->shouldReceive('getName')->once()->andReturn('name');

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::OAUTH2_TOKEN)->andReturn(null);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::OAUTH2_TOKENS)->andReturn(json_encode(array(
            'name' => array(
                'slug1' => array(
                    'access_token'  => 'slug1token',
                    'refresh_token' => 'slug1refresh',
                    'expiry_unix'   => '3333',
                    'extra'         => array(
                        'foo' => 'bar',
                    ),
                ),
                'slug2' => array(
                    'access_token'  => 'slug2token',
                    'refresh_token' => 'slug2refresh',
                    'expiry_unix'   => '777',
                    'extra'         => array(
                        'fooz' => 'baz',
                    ),
                ),
            ),
        )));

        $actual = $this->_sut->getStoredToken($mockProvider);

        $this->assertInstanceOf('tubepress_api_http_oauth_v2_TokenInterface', $actual);
    }

    /**
     * @dataProvider getData
     */
    public function testGetData($point, $getter, $stored, $expected)
    {
        $this->_mockPersistence->shouldReceive('fetch')->once()->with(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS)
            ->andReturn(json_encode($stored));
        $this->_mockArrayReader->shouldReceive('getAsString')->once()->with($stored, 'name.' . $point, null)->andReturnUsing(function ($arr, $path, $default) {

            $reader = new tubepress_array_impl_ArrayReader();

            return $reader->getAsString($arr, $path, $default);
        });

        $mockProvider = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $mockProvider->shouldReceive('getName')->once()->andReturn('name');

        $getter = 'get' . ucfirst($getter);
        $actual = $this->_sut->$getter($mockProvider);

        $this->assertEquals($expected, $actual);
    }

    public function getData()
    {
        return array(

            array('id', 'clientId', array(), null),
            array('id', 'clientId', array('name' => 'x'), null),
            array('id', 'clientId', array('name' => array()), null),
            array('id', 'clientId', array('name2' => array('id' => 'x')), null),
            array('id', 'clientId', array('name' => array('id2' => 'x')), null),
            array('id', 'clientId', array('name' => array('id' => 'x')), 'x'),

            array('secret', 'clientSecret', array(), null),
            array('secret', 'clientSecret', array('name' => 'x'), null),
            array('secret', 'clientSecret', array('name' => array()), null),
            array('secret', 'clientSecret', array('name2' => array('secret' => 'x')), null),
            array('secret', 'clientSecret', array('name' => array('secret2' => 'x')), null),
            array('secret', 'clientSecret', array('name' => array('secret' => 'x')), 'x'),
        );
    }
}
