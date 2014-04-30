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
 * @covers tubepress_impl_environment_SimpleEnvironmentDetector<extended>
 */
class tubepress_test_impl_environment_SimpleEnvironmentDetectorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_environment_SimpleEnvironmentDetector
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_environment_SimpleEnvironmentDetector();
        $this->_mockUrlFactory    = $this->createMockSingletonService(tubepress_spi_url_UrlFactoryInterface::_);
    }

    public function testVersion()
    {
        $latest = tubepress_spi_version_Version::parse('9.9.9');

        $current = $this->_sut->getVersion();

        $this->assertTrue($current instanceof tubepress_spi_version_Version);

        $this->assertTrue($latest->compareTo($current) === 0, "Expected $latest but got $current");
    }

    public function testIsPro()
    {
        $this->assertFalse($this->_sut->isPro());
    }

    public function testIsWordPress()
    {
        $this->assertFalse($this->_sut->isWordPress());
    }

    public function testGetUserContentDirNonWordPress()
    {
        $dir = TUBEPRESS_ROOT;

        $this->assertEquals("$dir/tubepress-content", $this->_sut->getUserContentDirectory());
    }

    public function testBaseUrl()
    {
        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl->shouldReceive('getScheme')->once()->andReturn('http');
        $mockUrl->shouldReceive('toString')->once()->andReturn('http://foo.com');
        $mockUrl->shouldReceive('getAuthority')->once()->andReturn('foo.com');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.com')->andReturn($mockUrl);
        $this->_sut->setBaseUrl('http://foo.com');

        $this->assertEquals('', $this->_sut->getBaseUrl());
    }

    public function testUserContentUrl()
    {
        $u = 'https://bar.com/xyz/test.php?some=thing#x';

        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl->shouldReceive('getScheme')->once()->andReturn('https');
        $mockUrl->shouldReceive('toString')->once()->andReturn($u);
        $mockUrl->shouldReceive('getAuthority')->once()->andReturn('bar.com');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with($u)->andReturn($mockUrl);

        $this->_sut->setUserContentUrl($u);

        $this->assertEquals('/xyz/test.php?some=thing#x', $this->_sut->getUserContentUrl());
    }

    public function testGetUserContentUrlNonWordPress()
    {
        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockUrl->shouldReceive('getScheme')->once()->andReturn('http');
        $mockUrl->shouldReceive('toString')->once()->andReturn('http://foo.bar/x');
        $mockUrl->shouldReceive('getAuthority')->once()->andReturn('foo.bar');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar/x')->andReturn($mockUrl);

        $this->_sut->setUserContentUrl('http://foo.bar/x');

        $this->assertEquals('/x', $this->_sut->getUserContentUrl());
    }
}
