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
class tubepress_addons_vimeo_impl_options_VimeoOptionsProvider implements tubepress_spi_options_PluggableOptionDescriptorProvider
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * Fetch all the option descriptors from this provider.
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    public function getOptionDescriptors()
    {
        $toReturn = array();

        /**
         * EMBEDDED PLAYER OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');                             //>(translatable)<
        $option->setDescription(sprintf('Default is %s', "999999")); //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;


        /**
         * FEED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');                                                                                        //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new" target="_blank">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');                                                                                     //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new" target="_blank">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $toReturn[] = $option;


        /**
         * GALLERY SOURCE OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setLabel('Videos from this Vimeo album');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setLabel('Videos this Vimeo user appears in');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setLabel('Videos in this Vimeo channel');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setLabel('Videos from this Vimeo group');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setLabel('Videos this Vimeo user likes');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('glacier national park');
        $option->setLabel('Vimeo search for');                   //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('AvantGardeDiaries');
        $option->setLabel('Videos uploaded by this Vimeo user'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;


        /**
         * META OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Meta::LIKES);
        $option->setLabel('Number of "likes"');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;
        
        return $toReturn;
    }
}