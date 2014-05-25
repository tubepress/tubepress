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
 * Registers a few extensions to allow TubePress to work with Vimeo.
 */
class tubepress_vimeo_impl_options_VimeoOptionProvider implements tubepress_core_api_options_EasyProviderInterface
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => 'Main color', //>(translatable)<
            
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY    => 'Vimeo API "Consumer Key"',    //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => 'Vimeo API "Consumer Secret"', //>(translatable)<
            
            tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE      => 'Videos from this Vimeo album',       //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE => 'Videos this Vimeo user appears in',  //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE    => 'Videos in this Vimeo channel',       //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',  //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE      => 'Videos from this Vimeo group',       //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE      => 'Videos this Vimeo user likes',       //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE     => 'Vimeo search for',                   //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE => 'Videos uploaded by this Vimeo user', //>(translatable)<
            
            tubepress_vimeo_api_const_options_Names::LIKES => 'Number of "likes"',  //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<
            
            tubepress_vimeo_api_const_options_Names::VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    public function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => '999999',

            tubepress_vimeo_api_const_options_Names::VIMEO_KEY    => null,
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => null,

            tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE      => '140484',
            tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE   => 'patricklawler',
            tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE      => 'hdxs',
            tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE      => 'coiffier',
            tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE     => 'glacier national park',
            tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE => 'AvantGardeDiaries',
            
            tubepress_vimeo_api_const_options_Names::LIKES => false,
        );
    }
    
    public function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => self::$_regexColor,

            tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE    => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE   => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE => self::$_regexWordChars,
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