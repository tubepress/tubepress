<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipantTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant
     */
    private $_sut;

    private $_mockFieldBuilder;

    public function onSetup() {

        $this->_sut = new tubepress_plugins_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant();

        $this->_mockFieldBuilder = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);

    }

    public function testGetName()
    {
        $this->assertEquals('jwplayer', $this->_sut->getName());
    }

    public function testGetFriendlyName()
    {
        $this->assertEquals('JW Player', $this->_sut->getFriendlyName());
    }

    public function testNonEmbeddedTab()
    {
        $this->assertEquals(array(), $this->_sut->getFieldsForTab((string) mt_rand()));
    }

    public function testGetFields()
    {
        $mockFields = array(

            Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME)
        );

        $optionNames = array(

            tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN
        );

        for ($x = 0; $x < 4; $x++) {

            $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

                $optionNames[$x],
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME

            )->andReturn($mockFields[$x]);
        }

        $result = $this->_sut->getFieldsForTab(tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME);

        $this->assertEquals($mockFields, $result);

    }
}

