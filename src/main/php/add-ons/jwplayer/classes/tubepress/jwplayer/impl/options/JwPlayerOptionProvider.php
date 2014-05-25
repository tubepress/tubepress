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
class tubepress_jwplayer_impl_options_JwPlayerOptionProvider implements tubepress_core_api_options_EasyProviderInterface
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'Background color',//>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => 'Front color',     //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => 'Light color',     //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => 'Screen color',    //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => sprintf('Default is %s', "FFFFFF"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => sprintf('Default is %s', "000000"),   //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    public function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'FFFFFF',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => '000000',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => '000000',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => '000000',
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    public function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => self::$_regexColor,
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    public function getMapOfOptionNamesToFixedAcceptableValues()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionNamesThatCannotBeSetViaShortcode()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionsNamesThatShouldNotBePersisted()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     */
    public function getOptionNamesWithDynamicDiscreteAcceptableValues()
    {
        return array();
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     */
    public function getDynamicDiscreteAcceptableValuesForOption($optionName)
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent positive integers.
     */
    public function getOptionNamesOfPositiveIntegers()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent non-negative integers.
     */
    public function getOptionNamesOfNonNegativeIntegers()
    {
        return array();
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    public function getAllProOptionNames()
    {
        return array();
    }
}