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

/**
 * Registers a few extensions to allow TubePress to work with Vimeo.
 */
final class tubepress_plugins_vimeo_Vimeo
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    public static function init()
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        /**
         * EMBEDDED PLAYER OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');                    //>(translatable)<
        $option->setDescription('Default is 999999.');      //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $odr->registerOptionDescriptor($option);


        /**
         * FEED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');                                                                                        //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');                                                                                     //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $odr->registerOptionDescriptor($option);


        /**
         * GALLERY SOURCE OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setLabel('Videos from this Vimeo album');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setLabel('Videos this Vimeo user appears in');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setLabel('Videos in this Vimeo channel');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setLabel('Videos from this Vimeo group');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setLabel('Videos this Vimeo user likes');       //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('cats playing piano');
        $option->setLabel('Vimeo search for');                   //>(translatable)<
        $option->setValidValueRegex('/[\w" ]+/');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('mattkaar');
        $option->setLabel('Videos uploaded by this Vimeo user'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);


        /**
         * META OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Meta::LIKES);
        $option->setLabel('Number of "likes"');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);
    }
}

tubepress_plugins_vimeo_Vimeo::init();