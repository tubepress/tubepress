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
 * @covers tubepress_addons_jwplayer_impl_options_JwPlayerOptionProvider<extended>
 */
class tubepress_test_addons_jwplayer_impl_listeners_boot_JwPlayerOptionProviderTest extends tubepress_test_impl_options_AbstractOptionProviderTest
{
    public function buildSut()
    {
        return new tubepress_addons_jwplayer_impl_options_JwPlayerOptionProvider();
    }

    public function testBadColor()
    {
        $this->getMockMessageService()->shouldReceive('_')->twice()->with('Background color')->andReturn('boo');
        $this->assertFalse($this->getSut()->isValid(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK, 'wrong!'));
        $this->assertEquals('Invalid value supplied for "boo".', $this->getSut()->getProblemMessage(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK, 'wrong!'));
    }

    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'Background color',//>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => 'Front color',     //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => 'Light color',     //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => 'Screen color',    //>(translatable)<
        );
    }

    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => sprintf('Default is %s', "FFFFFF"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => sprintf('Default is %s', "000000"),   //>(translatable)<
        );
    }

    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'FFFFFF',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => '000000',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => '000000',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => '000000',
        );
    }
}