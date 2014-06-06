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
 * @covers tubepress_core_environment_impl_Environment<extended>
 */
class tubepress_test_core_environment_impl_EnvironmentTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_environment_impl_Environment
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
        $this->_mockUrlFactory   = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $this->_mockBootSettings = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_sut              = new tubepress_core_environment_impl_Environment($this->_mockUrlFactory, $this->_mockBootSettings);
    }

    public function testVersion()
    {
        $latest = tubepress_core_version_api_Version::parse('9.9.9');
        $current = $this->_sut->getVersion();
        $this->assertTrue($current instanceof tubepress_core_version_api_Version);
        $this->assertTrue($latest->compareTo($current) === 0, "Expected $latest but got $current");
    }

    public function testIsProTrue()
    {
        $this->_sut->markAsPro();
        $this->assertTrue($this->_sut->isPro());
    }

    public function testIsWordPressTrue()
    {
        $this->_sut->setWpFunctionsInterface($this->mock(tubepress_wordpress_impl_wp_WpFunctions::_));
        $this->assertTrue($this->_sut->isWordPress());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid argument to tubepress_core_environment_impl_Environment::setWpFunctionsInterface
     */
    public function testBadWpInterface()
    {
        $this->_sut->setWpFunctionsInterface(new stdClass());
    }

    public function testIsProFalse()
    {
        $this->assertFalse($this->_sut->isPro());
    }

    public function testIsWordPressFalse()
    {
        $this->assertFalse($this->_sut->isWordPress());
    }

    public function testDetectUserContentUrlNonWp()
    {
        $mockUrl        = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockContentUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockContentUrl->shouldReceive('toString')->once()->andReturn('abc');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('yellow')->andReturn($mockContentUrl);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc/tubepress-content')->andReturn($mockUrl);
        $this->_sut->setBaseUrl('yellow');
        $result = $this->_sut->getUserContentUrl();
        $this->assertSame($mockUrl, $result);
    }

    public function testDetectUserContentUrlWp()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockWp  = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $mockWp->shouldReceive('content_url')->once()->andReturn('xyz');
        $this->_sut->setWpFunctionsInterface($mockWp);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('xyz/tubepress-content')->andReturn($mockUrl);
        $result = $this->_sut->getUserContentUrl();
        $this->assertSame($mockUrl, $result);
    }

    public function testUserContentDir()
    {
        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn('yyy');
        $this->assertEquals('yyy', $this->_sut->getUserContentDirectory());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetUserContentUrlFromDefineBad()
    {
        define('TUBEPRESS_CONTENT_URL', 'yoyo');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('yoyo')->andThrow(new InvalidArgumentException());
        $result = $this->_sut->getUserContentUrl();

        $this->assertNull($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetUserContentUrlFromDefineGood()
    {
        define('TUBEPRESS_CONTENT_URL', 'yoyo');

        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('yoyo')->andReturn($mockUrl);
        $result = $this->_sut->getUserContentUrl();

        $this->assertSame($mockUrl, $result);
    }

    public function testDefaults()
    {
        $this->assertNull($this->_sut->getBaseUrl());
        $this->assertNull($this->_sut->getUserContentUrl());
    }

    public function testSetUserContentUrlAsRealUrl()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_sut->setUserContentUrl($mockUrl);
        $this->assertSame($mockUrl, $this->_sut->getUserContentUrl());
    }

    public function testSetUserContentUrlAsString()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc')->andReturn($mockUrl);
        $this->_sut->setUserContentUrl('abc');
        $this->assertSame($mockUrl, $this->_sut->getUserContentUrl());
    }

    public function testSetBaseUrlAsRealUrl()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_sut->setBaseUrl($mockUrl);
        $this->assertSame($mockUrl, $this->_sut->getBaseUrl());
    }

    public function testSetBaseUrlAsString()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('abc')->andReturn($mockUrl);
        $this->_sut->setBaseUrl('abc');
        $this->assertSame($mockUrl, $this->_sut->getBaseUrl());
    }
}
