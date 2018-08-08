<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_api_options_Reference<extended>
 */
class tubepress_test_options_api_ReferenceTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_options_Reference
     */
    private $_sut;

    public function onSetup()
    {
        $valueMap = array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE=> array(
                'foo' => 'bar',
                'hi'  => true,
                'hey' => 33,
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                'hi' => 'description for hi',
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                'hey' => 'label for hey'
            )
        );

        $boolMap = array(
            tubepress_api_options_Reference::PROPERTY_NO_PERSIST => array(
                'hey'
            ),
            tubepress_api_options_Reference::PROPERTY_PRO_ONLY => array(
                'foo'
            ),
            tubepress_api_options_Reference::PROPERTY_NO_SHORTCODE => array()
        );

        $this->_sut = new tubepress_api_options_Reference(

            $valueMap, $boolMap
        );
    }

    public function testBasics()
    {
        $this->assertTrue($this->_sut->optionExists('foo'));
        $this->assertTrue($this->_sut->optionExists('hi'));
        $this->assertTrue($this->_sut->optionExists('hey'));
        $this->assertFalse($this->_sut->optionExists('food'));

        $this->assertEquals(array('foo', 'hi', 'hey'), $this->_sut->getAllOptionNames());

        $this->assertEquals('bar', $this->_sut->getDefaultValue('foo'));
        $this->assertEquals(true, $this->_sut->getDefaultValue('hi'));
        $this->assertEquals(33, $this->_sut->getDefaultValue('hey'));

        $this->assertNull($this->_sut->getUntranslatedLabel('foo'));
        $this->assertNull($this->_sut->getUntranslatedLabel('hi'));
        $this->assertEquals('label for hey', $this->_sut->getUntranslatedLabel('hey'));

        $this->assertNull($this->_sut->getUntranslatedDescription('foo'));
        $this->assertNull($this->_sut->getUntranslatedDescription('hey'));
        $this->assertEquals('description for hi', $this->_sut->getUntranslatedDescription('hi'));

        $this->assertFalse($this->_sut->isProOnly('hi'));
        $this->assertFalse($this->_sut->isProOnly('hey'));
        $this->assertTrue($this->_sut->isProOnly('foo'));

        $this->assertTrue($this->_sut->isMeantToBePersisted('hi'));
        $this->assertTrue($this->_sut->isMeantToBePersisted('foo'));
        $this->assertFalse($this->_sut->isMeantToBePersisted('hey'));

        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('hi'));
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('foo'));
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('hey'));

        $this->assertTrue($this->_sut->isBoolean('hi'));
        $this->assertFalse($this->_sut->isBoolean('foo'));
        $this->assertFalse($this->_sut->isBoolean('hey'));
    }
}