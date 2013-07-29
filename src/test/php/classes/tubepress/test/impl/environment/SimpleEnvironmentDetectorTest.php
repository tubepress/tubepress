<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_impl_environment_SimpleEnvironmentDetectorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_environment_SimpleEnvironmentDetector
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_environment_SimpleEnvironmentDetector();
    }

    public function testVersion()
    {
        $latest = tubepress_spi_version_Version::parse('3.1.0');

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
        $this->_sut->setBaseUrl('http://foo.com');

        $this->assertEquals('http://foo.com', $this->_sut->getBaseUrl());
    }

}
