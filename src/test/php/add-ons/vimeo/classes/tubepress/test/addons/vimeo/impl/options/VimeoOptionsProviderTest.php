<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_vimeo_impl_options_VimeoOptionsProvider
 */
class tubepress_test_addons_vimeo_impl_options_VimeoOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
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
        $option->setLabel('Vimeo API "Consumer Key"');
        $option->setDescription('<a href="http://vimeo.com/api/applications/new" target="_blank">Click here</a> to register for a consumer key and secret.');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');
        $option->setDescription('<a href="http://vimeo.com/api/applications/new" target="_blank">Click here</a> to register for a consumer key and secret.');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('AvantGardeDiaries');
        $option->setLabel('Videos uploaded by this Vimeo user');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setLabel('Videos this Vimeo user likes');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setLabel('Videos this Vimeo user appears in');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('glacier national park');
        $option->setLabel('Vimeo search for');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setLabel('Videos in this Vimeo channel');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setLabel('Videos from this Vimeo group');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setLabel('Videos from this Vimeo album');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Meta::LIKES);
        $option->setLabel('Number of "likes"');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');
        $option->setDescription('Default is 999999');
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        return $toReturn;
    }
}