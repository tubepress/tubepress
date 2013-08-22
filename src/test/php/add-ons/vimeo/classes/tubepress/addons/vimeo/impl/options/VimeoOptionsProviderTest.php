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
class tubepress_addons_vimeo_impl_options_VimeoOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    public function prepare(tubepress_spi_options_PluggableOptionDescriptorProvider $sut)
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
    }

    /**
     * @return tubepress_spi_options_PluggableOptionDescriptorProvider
     */
    protected function buildSut()
    {
        return new tubepress_addons_vimeo_impl_options_VimeoOptionsProvider();
    }

    /**
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    protected function getExpectedOptions()
    {
        $toReturn = array();
        
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');                                                                                        //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');                                                                                     //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('AvantGardeDiaries');
        $option->setLabel('Videos uploaded by this Vimeo user');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setLabel('Videos this Vimeo user likes');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setLabel('Videos this Vimeo user appears in');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('cats playing piano');
        $option->setLabel('Vimeo search for');  //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setLabel('Videos in this Vimeo channel');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setLabel('Videos from this Vimeo group');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setLabel('Videos from this Vimeo album');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Meta::LIKES);
        $option->setLabel('Number of "likes"');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');               //>(translatable)<
        $option->setDescription('Default is 999999'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        return $toReturn;
    }
}