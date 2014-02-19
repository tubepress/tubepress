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
class tubepress_addons_vimeo_impl_options_VimeoOptionProvider extends tubepress_impl_options_AbstractOptionProvider
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR => 'Main color', //>(translatable)<
            
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY    => 'Vimeo API "Consumer Key"',    //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET => 'Vimeo API "Consumer Secret"', //>(translatable)<
            
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE      => 'Videos from this Vimeo album',       //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => 'Videos this Vimeo user appears in',  //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE    => 'Videos in this Vimeo channel',       //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',  //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE      => 'Videos from this Vimeo group',       //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE      => 'Videos this Vimeo user likes',       //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE     => 'Vimeo search for',                   //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => 'Videos uploaded by this Vimeo user', //>(translatable)<
            
            tubepress_addons_vimeo_api_const_options_names_Meta::LIKES => 'Number of "likes"',  //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<
            
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR => '999999',

            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY    => null,
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET => null,

            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE      => '140484',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE   => 'patricklawler',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE      => 'hdxs',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE      => 'coiffier',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE     => 'glacier national park',
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => 'AvantGardeDiaries',
            
            tubepress_addons_vimeo_api_const_options_names_Meta::LIKES => false,
        );
    }
    
    protected function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR => self::$_regexColor,

            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE      => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE    => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE   => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE      => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE      => self::$_regexWordChars,
            tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => self::$_regexWordChars,
        );
    }
}