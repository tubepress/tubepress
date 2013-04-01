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
class tubepress_impl_player_DefaultPluginRegistryTest extends TubePressUnitTest
{
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_plugin_DefaultPluginRegistry();
    }


    public function testLoadGoodPlugin()
    {
        $plugin = new tubepress_impl_plugin_PluginBase(

            'something',
            'hello',
            '1.2.3',
            'b',
            __DIR__ . '/../../../../../resources/plugins/good_plugin',
            array(), array(), array()
        );

        $this->_sut->load($plugin);

        $this->assertTrue(defined('GOOD_PLUGIN_LOADED'));
        $this->assertTrue(GOOD_PLUGIN_LOADED === true);
    }
}