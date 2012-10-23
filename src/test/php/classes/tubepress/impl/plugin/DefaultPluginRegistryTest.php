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
class tubepress_impl_player_DefaultPluginRegistryTest extends TubePressUnitTest
{
    private $_sut;

    public function setUp()
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
            array(), array()
        );

        $this->_sut->load($plugin);

        $this->assertTrue(defined('GOOD_PLUGIN_LOADED'));
        $this->assertTrue(GOOD_PLUGIN_LOADED === true);
    }
}