<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_coreapiservices_impl_url_CurrentUrlService
 */
class tubepress_test_addons_coreapiservices_url_CurrentUrlServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_coreapiservices_impl_url_CurrentUrlService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockUrlFactory = ehough_mockery_Mockery::mock(tubepress_api_url_UrlFactoryInterface::_);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage foobar
     * @runInSeparateProcess
     */
    public function testCannotGetUrlBadServerVars()
    {
        $serverArray = array(
            'SERVER_NAME' => '(#$#$#$#$#$#%%***%**%',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/foo/bar',
        );

        $sut = new tubepress_addons_coreapiservices_impl_url_CurrentUrlService($serverArray, $this->_mockUrlFactory);

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://(#$#$#$#$#$#%%***%**%/foo/bar')
            ->andThrow(new InvalidArgumentException('foobar'));

        $sut->getUrl();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Missing $_SERVER variable: SERVER_PORT
     * @runInSeparateProcess
     */
    public function testCannotGetUrlMissingServerVars()
    {
        $sut = new tubepress_addons_coreapiservices_impl_url_CurrentUrlService(array(), $this->_mockUrlFactory);
        $sut->getUrl();
    }

    /**
     * @runInSeparateProcess
     * @dataProvider dataProviderTestGetFullUrl
     */
    public function testGetFullUrl($serverArray, $expectedUrl)
    {
        $sut = new tubepress_addons_coreapiservices_impl_url_CurrentUrlService($serverArray, $this->_mockUrlFactory);

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with($expectedUrl)->andReturn($mockUrl);

        $result = $sut->getUrl();

        $this->assertSame($mockUrl, $result);
    }

    public function dataProviderTestGetFullUrl()
    {
        return array(
            array(
                array(
                    'HTTPS'       => 'on',
                    'SERVER_PORT' => 33,
                    'SERVER_NAME' => 'host.name',
                    'REQUEST_URI' => '/foo/bar.php?test=foo&bla'
                ),
                'https://host.name:33/foo/bar.php?test=foo&bla'
            ),
            array(
                array(
                    'HTTPS'       => 'off',
                    'SERVER_PORT' => 44,
                    'SERVER_NAME' => 'foo.name',
                    'REQUEST_URI' => '/bar/bar.php?test=foo&bla'
                ),
                'http://foo.name:44/bar/bar.php?test=foo&bla'
            ),
            array(
                array(
                    'SERVER_PORT' => 80,
                    'SERVER_NAME' => 'foo.bar',
                    'REQUEST_URI' => '/bar/foo.php?test=foo&bla'
                ),
                'http://foo.bar/bar/foo.php?test=foo&bla'
            ),
        );
    }
}