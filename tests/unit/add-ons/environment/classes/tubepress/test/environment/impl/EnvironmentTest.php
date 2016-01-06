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
 * @covers tubepress_environment_impl_Environment<extended>
 */
class tubepress_test_environment_impl_EnvironmentTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_environment_impl_Environment
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootSettings;

    public function onSetup()
    {
        $this->_mockUrlFactory   = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockBootSettings = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_sut              = new tubepress_environment_impl_Environment($this->_mockUrlFactory, $this->_mockBootSettings);
    }

    public function testVersion()
    {
        $latest = tubepress_api_version_Version::parse('99.99.99');
        $current = $this->_sut->getVersion();
        $this->assertTrue($current instanceof tubepress_api_version_Version);
        $this->assertTrue($latest->compareTo($current) === 0, "Expected $latest but got $current");
    }

    public function testIsProTrue()
    {
        $this->_sut->markAsPro();
        $this->assertTrue($this->_sut->isPro());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid argument to tubepress_environment_impl_Environment::setWpFunctionsInterface
     */
    public function testBadWpInterface()
    {
        $this->_sut->setWpFunctionsInterface(new stdClass());
    }

    public function testIsProFalse()
    {
        $this->assertFalse($this->_sut->isPro());
    }

    public function testDetectUserContentUrlNoBootSettingsNonWp()
    {
        $mockUrl        = $this->mock('tubepress_api_url_UrlInterface');
        $mockContentUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockContentUrl->shouldReceive('toString')->once()->andReturn('abc');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('yellow')->andReturn($mockContentUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc/tubepress-content')->andReturn($mockUrl);

        $this->_mockBootSettings->shouldReceive('getUrlUserContent')->once()->andReturnNull();

        $mockUrl->shouldReceive('freeze')->once();
        $mockContentUrl->shouldReceive('freeze')->once();

        $this->_sut->setBaseUrl('yellow');
        $result = $this->_sut->getUserContentUrl();
        $this->assertSame($mockUrl, $result);
    }

    public function testGetBaseUrlNoBootSettingsNonWp()
    {
        $this->_mockBootSettings->shouldReceive('getUrlBase')->once()->andReturnNull();

        $this->setExpectedException('RuntimeException', 'Please specify TubePress base URL in tubepress-content/config/settings.php');

        $this->_sut->getBaseUrl();
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetBaseUrlNoBootSettingsRegularWp()
    {
        define('ABSPATH', 'some abspath');
        define('DB_USER', 'user');

        $this->_mockBootSettings->shouldReceive('getUrlBase')->once()->andReturnNull();

        $mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_sut->setWpFunctionsInterface($mockWpFunctions);

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('wp content url');

        $mockBaseUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('freeze')->once();

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('wp content url/plugins/core')->andReturn($mockBaseUrl);

        $actual = $this->_sut->getBaseUrl();

        $this->assertSame($mockBaseUrl, $actual);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetBaseUrlNoBootSettingsMultiWpSsl()
    {
        define('ABSPATH', 'some abspath');
        define('DB_USER', 'user');
        define('DOMAIN_MAPPING', true);
        define('COOKIE_DOMAIN', 'gimme cookies!');

        $this->_mockBootSettings->shouldReceive('getUrlBase')->once()->andReturnNull();

        $mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_sut->setWpFunctionsInterface($mockWpFunctions);

        $mockWpFunctions->shouldReceive('is_ssl')->once()->andReturn(true);

        $mockBaseUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('freeze')->once();

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://gimme cookies!/wp-content/plugins/core')->andReturn($mockBaseUrl);

        $actual = $this->_sut->getBaseUrl();

        $this->assertSame($mockBaseUrl, $actual);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetBaseUrlNoBootSettingsMultiWpNoSsl()
    {
        define('ABSPATH', 'some abspath');
        define('DB_USER', 'user');
        define('DOMAIN_MAPPING', true);
        define('COOKIE_DOMAIN', 'gimme cookies!');

        $this->_mockBootSettings->shouldReceive('getUrlBase')->once()->andReturnNull();

        $mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_sut->setWpFunctionsInterface($mockWpFunctions);

        $mockWpFunctions->shouldReceive('is_ssl')->once()->andReturn(false);

        $mockBaseUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('freeze')->once();

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://gimme cookies!/wp-content/plugins/core')->andReturn($mockBaseUrl);

        $actual = $this->_sut->getBaseUrl();

        $this->assertSame($mockBaseUrl, $actual);
    }

    public function testGetUserContentUrlFromBootSettingsGood()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockBootSettings->shouldReceive('getUrlUserContent')->once()->andReturn($mockUrl);

        $result = $this->_sut->getUserContentUrl();

        $this->assertSame($mockUrl, $result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetUserContentUrlWp()
    {
        define('ABSPATH', 'some abspath');
        define('DB_USER', 'user');

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockBootSettings->shouldReceive('getUrlUserContent')->once()->andReturn(null);

        $mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_sut->setWpFunctionsInterface($mockWpFunctions);

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('wp content url');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('wp content url/tubepress-content')->andReturn($mockUrl);
        $mockUrl->shouldReceive('freeze')->once();

        $result = $this->_sut->getUserContentUrl();

        $this->assertSame($mockUrl, $result);
    }

    public function testGetAjaxUrlFromBootSettingsGood()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockBootSettings->shouldReceive('getUrlAjaxEndpoint')->once()->andReturn($mockUrl);

        $result = $this->_sut->getAjaxEndpointUrl();

        $this->assertSame($mockUrl, $result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetAjaxUrlWp()
    {
        define('ABSPATH', 'some abspath');
        define('DB_USER', 'user');

        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockBootSettings->shouldReceive('getUrlAjaxEndpoint')->once()->andReturn(null);

        $mockWpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_sut->setWpFunctionsInterface($mockWpFunctions);

        $mockWpFunctions->shouldReceive('admin_url')->once()->with('admin-ajax.php')->andReturn('wp admin url');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('wp admin url')->andReturn($mockUrl);
        $mockUrl->shouldReceive('freeze')->once();

        $result = $this->_sut->getAjaxEndpointUrl();

        $this->assertSame($mockUrl, $result);
    }

    public function testDefaults()
    {
        $mockBaseUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserUrl = $this->mock('tubepress_api_url_UrlInterface');

        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('foobar');
        $mockUserUrl->shouldReceive('freeze')->once();

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('foobar/tubepress-content')->andReturn($mockUserUrl);

        $this->_mockBootSettings->shouldReceive('getUrlBase')->atLeast(1)->andReturn($mockBaseUrl);
        $this->_mockBootSettings->shouldReceive('getUrlUserContent')->once()->andReturnNull();

        $this->assertSame($mockBaseUrl, $this->_sut->getBaseUrl());
        $this->assertSame($mockUserUrl, $this->_sut->getUserContentUrl());
    }

    public function testSetUserContentUrlAsRealUrl()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');

        $mockUrl->shouldReceive('freeze')->once();

        $this->_sut->setUserContentUrl($mockUrl);
        $this->assertSame($mockUrl, $this->_sut->getUserContentUrl());
    }

    public function testSetUserContentUrlAsString()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc')->andReturn($mockUrl);
        $mockUrl->shouldReceive('freeze')->once();
        $this->_sut->setUserContentUrl('abc');
        $this->assertSame($mockUrl, $this->_sut->getUserContentUrl());
    }

    public function testSetBaseUrlAsRealUrl()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockUrl->shouldReceive('freeze')->once();
        $this->_sut->setBaseUrl($mockUrl);
        $this->assertSame($mockUrl, $this->_sut->getBaseUrl());
    }

    public function testSetBaseUrlAsString()
    {
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc')->andReturn($mockUrl);
        $mockUrl->shouldReceive('freeze')->once();
        $this->_sut->setBaseUrl('abc');
        $this->assertSame($mockUrl, $this->_sut->getBaseUrl());
    }
}
