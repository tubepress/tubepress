<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class tubepress_impl_environment_SimpleEnvironmentDetectorTest extends TubePressUnitTest
{
    private $_sut;

    function onSetup()
    {
        $this->_sut = new tubepress_impl_environment_SimpleEnvironmentDetector();
    }

    function testVersion()
    {
        $latest = tubepress_spi_version_Version::parse('2.5.0');

        $current = $this->_sut->getVersion();

        $this->assertTrue($current instanceof tubepress_spi_version_Version);

        $this->assertTrue($latest->compareTo($current) === 0, "Expected $latest but got $current");
    }

    function testIsPro()
    {
        $this->assertFalse($this->_sut->isPro());
    }

    function testIsWordPress()
    {
        $this->assertFalse($this->_sut->isWordPress());
    }

    public function testGetUserContentDirNonWordPress()
    {
        $dir = TUBEPRESS_ROOT;

        $this->assertEquals("$dir/tubepress-content", $this->_sut->getUserContentDirectory());
    }

}
