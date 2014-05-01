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
 * @covers tubepress_addons_vimeo_impl_options_VimeoOptionProvider<extended>
 */
class tubepress_test_addons_vimeo_impl_options_VimeoOptionProviderTest extends tubepress_test_impl_options_AbstractOptionProviderTest
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @return tubepress_spi_options_OptionProvider
     */
    protected function buildSut()
    {
        return new tubepress_addons_vimeo_impl_options_VimeoOptionProvider($this->getMockMessageService(), $this->getMockEventDispatcher());
    }

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

    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<

            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
            tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
        );
    }
}