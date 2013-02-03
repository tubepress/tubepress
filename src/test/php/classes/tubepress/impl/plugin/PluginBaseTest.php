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
class tubepress_impl_player_PluginBaseTest extends TubePressUnitTest
{
    public function testBuildsCorrectly1()
    {
        $sut = new tubepress_impl_plugin_PluginBase(

            'name',
            'description',
            '1.0.0',
            'short',
            'absPath',
            array(),
            array(),
            array()
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
            'absPath',
            array(),
            array(),
            array()
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('absPath', $sut->getAbsolutePathOfDirectory());
        $this->assertEquals('short', $sut->getFileNameWithoutExtension());
    }

    public function testBuildsCorrectly3()
    {
        $mockExtension = Mockery::mock('ehough_iconic_api_extension_IExtension');

        $sut = new tubepress_impl_plugin_PluginBase(

            'name',
            'description',
            tubepress_spi_version_Version::parse('5.6.4'),
            'short',
            'absPath',
            array($mockExtension),
            array(),
            array()
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('5.6.4', (string) $sut->getVersion());
        $this->assertEquals('absPath', $sut->getAbsolutePathOfDirectory());
        $this->assertEquals('short', $sut->getFileNameWithoutExtension());
        $this->assertEquals(array($mockExtension), $sut->getIocContainerExtensions());
    }
}