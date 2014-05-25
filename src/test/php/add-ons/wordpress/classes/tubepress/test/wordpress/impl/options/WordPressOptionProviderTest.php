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
 * @covers tubepress_wordpress_impl_options_WordPressOptionProvider<extended>
 */
class tubepress_test_wordpress_impl_options_WordPressOptionProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_options_WordPressOptionProvider
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_wordpress_impl_options_WordPressOptionProvider();
    }

    protected function getMapOfOptionNamesToDefaultValues()
    {
        $expected = array(

            tubepress_wordpress_api_const_OptionNames::WIDGET_TITLE     => 'TubePress',
            tubepress_wordpress_api_const_OptionNames::WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
        );
        $actual = $this->_sut->getMapOfOptionNamesToDefaultValues();
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