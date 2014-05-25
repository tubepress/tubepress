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
 * @covers tubepress_vimeo_impl_options_VimeoOptionProvider<extended>
 */
class tubepress_test_vimeo_impl_options_VimeoOptionProviderTest extends tubepress_test_TubePressUnitTest
{
    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @var tubepress_vimeo_impl_options_VimeoOptionProvider
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_vimeo_impl_options_VimeoOptionProvider();
    }

    public function testMapOfOptionNamesToDefaultValues()
    {
        $expected = array(

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

        $actual = $this->_sut->getMapOfOptionNamesToDefaultValues();
        $this->assertEquals($expected, $actual);
    }

    public function testMapOfOptionNamesToUntranslatedLabels()
    {
        $expected = array(

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

        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedLabels();
        $this->assertEquals($expected, $actual);
    }

    public function testMapOfOptionNamesToUntranslatedDescriptions()
    {
        $expected = array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => sprintf('Default is %s', "999999"), //>(translatable)<

            tubepress_vimeo_api_const_options_Names::VIMEO_KEY    => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
            tubepress_vimeo_api_const_options_Names::VIMEO_SECRET => sprintf('<a href="%s" target="_blank">Click here</a> to register for a consumer key and secret.', "https://developer.vimeo.com/apps/new"), //>(translatable)<
        );

        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedDescriptions();
        $this->assertEquals($expected, $actual);
    }

    public function testMapOfOptionNamesToValidValueRegexes()
    {
        $expected = array(

            tubepress_vimeo_api_const_options_Names::PLAYER_COLOR => self::$_regexColor,

            tubepress_vimeo_api_const_options_Names::VIMEO_ALBUM_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_APPEARS_IN_VALUE => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_CHANNEL_VALUE    => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_CREDITED_VALUE   => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_GROUP_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_LIKES_VALUE      => self::$_regexWordChars,
            tubepress_vimeo_api_const_options_Names::VIMEO_UPLOADEDBY_VALUE => self::$_regexWordChars,
        );

        $actual = $this->_sut->getMapOfOptionNamesToValidValueRegexes();
        $this->assertEquals($expected, $actual);
    }

    public function testEmpties()
    {
        $expected = array();

        $this->assertEquals($expected, $this->_sut->getMapOfOptionNamesToFixedAcceptableValues());
        $this->assertEquals($expected, $this->_sut->getOptionNamesOfNonNegativeIntegers());
        $this->assertEquals($expected, $this->_sut->getOptionNamesOfPositiveIntegers());
        $this->assertEquals($expected, $this->_sut->getOptionsNamesThatShouldNotBePersisted());
        $this->assertEquals($expected, $this->_sut->getOptionNamesThatCannotBeSetViaShortcode());
        $this->assertEquals($expected, $this->_sut->getDynamicDiscreteAcceptableValuesForOption('s'));
        $this->assertEquals($expected, $this->_sut->getOptionNamesWithDynamicDiscreteAcceptableValues());
        $this->assertEquals($expected, $this->_sut->getAllProOptionNames());
    }
}