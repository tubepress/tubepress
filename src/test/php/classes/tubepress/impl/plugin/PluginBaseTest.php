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
class tubepress_impl_player_PluginBaseTest extends TubePressUnitTest
{
    public function testBuildsCorrectly1()
    {
        $sut = new tubepress_impl_plugin_PluginBase(

            'name',
            'description',
            '1.0.0',
            'short',
            'absPath'
        );
        
        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('absPath', $sut->getAbsolutePathOfDirectory());
        $this->assertEquals('short', $sut->getFileNameWithoutExtension());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBuildsCorrectly2()
    {
        $sut = new tubepress_impl_plugin_PluginBase(

            'name',
            'description',
            'x.y.z',
            'short',
            'absPath'
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('absPath', $sut->getAbsolutePathOfDirectory());
        $this->assertEquals('short', $sut->getFileNameWithoutExtension());
    }

    public function testBuildsCorrectly3()
    {
        $sut = new tubepress_impl_plugin_PluginBase(

            'name',
            'description',
            tubepress_spi_version_Version::parse('5.6.4'),
            'short',
            'absPath'
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('5.6.4', (string) $sut->getVersion());
        $this->assertEquals('absPath', $sut->getAbsolutePathOfDirectory());
        $this->assertEquals('short', $sut->getFileNameWithoutExtension());
    }
}