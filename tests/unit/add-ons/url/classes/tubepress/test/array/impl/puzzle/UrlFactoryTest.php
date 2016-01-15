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
 * @covers tubepress_url_impl_puzzle_UrlFactory<extended>
 */
class tubepress_test_url_impl_puzzle_UrlFactoryTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_url_impl_puzzle_UrlFactory
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_url_impl_puzzle_UrlFactory(array());
    }

    public function testValid()
    {
        $u = $this->_sut->fromString('https://foo.bar/one/two.php?test=true#frag');
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $u);
        $this->assertEquals('https://foo.bar/one/two.php?test=true#frag', "$u");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tubepress_url_impl_puzzle_UrlFactory::fromString() can only accept strings.
     */
    public function testInvalid()
    {
        $this->_sut->fromString(array('bad!'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse malformed url: http://(#$#$#$#$#$#%%***%**%:dfgdfgdgdfgdfg*(&*&&*foo/bar
     * @runInSeparateProcess
     */
    public function testCannotGetUrlBadServerVars()
    {
        $serverArray = array(
            'SERVER_NAME' => '(#$#$#$#$#$#%%***%**%',
            'SERVER_PORT' => 'dfgdfgdgdfgdfg',
            'REQUEST_URI' => '*(&*&&*foo/bar',
        );

        $sut = new tubepress_url_impl_puzzle_UrlFactory($serverArray);

        $sut->fromCurrent();
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Missing $_SERVER variable: SERVER_PORT
     * @runInSeparateProcess
     */
    public function testCannotGetUrlMissingServerVars()
    {
        $sut = new tubepress_url_impl_puzzle_UrlFactory(array());
        $sut->fromCurrent();
    }

    /**
     * @runInSeparateProcess
     * @dataProvider dataProviderTestGetFullUrl
     */
    public function testGetFullUrl($serverArray, $expectedUrl)
    {
        $sut = new tubepress_url_impl_puzzle_UrlFactory($serverArray);

        $result = $sut->fromCurrent();

        $this->assertSame($expectedUrl, $result->toString());
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