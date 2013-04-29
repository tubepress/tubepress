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
class tubepress_test_impl_player_DefaultAddonLoaderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_DefaultAddonLoader
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_addon_DefaultAddonLoader();
    }

    public function testBootstrapClass()
    {
        $addon = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);

        $addon->shouldReceive('getName')->once()->andReturn('fooey');

        $addon->shouldReceive('getBootstrap')->once()->andReturn('ValidBootstrapper');

        $result = $this->_sut->load($addon);

        $this->assertNull($result);
    }

    public function testBootstrapFileThrowsException()
    {
        $addon = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);

        $addon->shouldReceive('getName')->once()->andReturn('fooey');

        $tempFile = tempnam(sys_get_temp_dir(), 'tubepress-testBootstrapThrowsException');
        $handle = fopen($tempFile, 'w');
        fwrite($handle, '<?php throw new Exception("Hi");');
        fclose($handle);

        $addon->shouldReceive('getBootstrap')->once()->andReturn($tempFile);
        $addon->shouldReceive('getName')->once()->andReturn('some add-on');

        $result = $this->_sut->load($addon);

        $this->assertEquals('Hit exception when trying to load some add-on: Hi', $result);

        unlink($tempFile);
    }

    public function testBootstrapFile()
    {
        $addon = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);

        $addon->shouldReceive('getName')->once()->andReturn('fooey');

        $tempFile = tempnam(sys_get_temp_dir(), 'tubepress-testLoadGoodAddon');

        $addon->shouldReceive('getBootstrap')->once()->andReturn($tempFile);

        $result = $this->_sut->load($addon);

        $this->assertNull($result);
    }
}

class ValidBootstrapper
{
    public $bell;

    public function boot()
    {
        $this->bell = true;
    }
}