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
 * @covers tubepress_jwplayer_impl_options_JwPlayerOptionProvider<extended>
 */
class tubepress_test_jwplayer_impl_options_JwPlayerOptionProviderTest extends tubepress_test_TubePressUnitTest
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * @var tubepress_jwplayer_impl_options_JwPlayerOptionProvider
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_jwplayer_impl_options_JwPlayerOptionProvider();
    }

    public function testGetLabels()
    {
        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedLabels();
        $expected = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'Background color',//>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => 'Front color',     //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => 'Light color',     //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => 'Screen color',    //>(translatable)<
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetDescriptions()
    {
        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedDescriptions();
        $expected = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => sprintf('Default is %s', "FFFFFF"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => sprintf('Default is %s', "000000"),   //>(translatable)<
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => sprintf('Default is %s', "000000"),   //>(translatable)<
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetDefaultValues()
    {
        $actual = $this->_sut->getMapOfOptionNamesToDefaultValues();
        $expected = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => 'FFFFFF',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => '000000',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => '000000',
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => '000000',
        );
        $this->assertEquals($expected, $actual);
    }

    public function testGetRegexes()
    {
        $actual = $this->_sut->getMapOfOptionNamesToValidValueRegexes();
        $expected = array(

            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_BACK   => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_FRONT  => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT  => self::$_regexColor,
            tubepress_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN => self::$_regexColor,
        );
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