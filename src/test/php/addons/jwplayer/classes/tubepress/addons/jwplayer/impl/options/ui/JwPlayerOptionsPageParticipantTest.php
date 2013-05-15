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
class tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipantTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldBuilder;

    public function onSetup() {

        $this->_sut = new tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant();

        $this->_mockFieldBuilder = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
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
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_UI_FIELDS_FOR_TAB, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_api_event_EventInterface && is_array($event->getSubject()) && $event->getArgument('participant') instanceof tubepress_spi_options_ui_PluggableOptionsPageParticipant;
        }));

        $this->assertEquals(array(), $this->_sut->getFieldsForTab((string) mt_rand()));
    }

    public function testGetFields()
    {
        $mockFields = array(

            ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME),
            ehough_mockery_Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME)
        );

        $optionNames = array(

            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN
        );

        for ($x = 0; $x < 4; $x++) {

            $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

                $optionNames[$x],
                tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME

            )->andReturn($mockFields[$x]);
        }

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_UI_FIELDS_FOR_TAB, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_api_event_EventInterface && is_array($event->getSubject()) && $event->getArgument('participant') instanceof tubepress_spi_options_ui_PluggableOptionsPageParticipant;
        }));

        $result = $this->_sut->getFieldsForTab(tubepress_impl_options_ui_tabs_EmbeddedTab::TAB_NAME);

        $this->assertEquals($mockFields, $result);

    }
}

