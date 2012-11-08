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

