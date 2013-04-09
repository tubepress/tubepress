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
class tubepress_impl_player_DefaultAddonLoaderTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_DefaultAddonLoader
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_addon_DefaultAddonLoader();

        $this->_bell = false;
    }


    public function testLoadGoodPlugin()
    {
        $plugin = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);

        $plugin->shouldReceive('getBootstrap')->once()->andReturn(realpath(dirname(__FILE__) . '/../../../../../resources/plugins/fakeBootstrap.php'));

        $this->_sut->load($plugin);

        $this->assertTrue(defined('GOOD_addon_LOADED'));
        $this->assertTrue(GOOD_addon_LOADED === true);
    }

    public function boot()
    {

    }
}