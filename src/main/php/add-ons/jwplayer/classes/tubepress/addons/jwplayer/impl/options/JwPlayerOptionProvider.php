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
 * Hooks JW Player into TubePress.
 */
class tubepress_addons_jwplayer_impl_options_JwPlayerOptionProvider extends tubepress_impl_options_AbstractOptionProvider
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'Background color',//>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => 'Front color',     //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => 'Light color',     //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => 'Screen color',    //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => sprintf('Default is %s', "FFFFFF"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => sprintf('Default is %s', "000000"),   //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'FFFFFF',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => '000000',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => '000000',
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => '000000',
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    protected function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => self::$_regexColor,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => self::$_regexColor,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => self::$_regexColor,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => self::$_regexColor,
        );
    }
}