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
 * @covers tubepress_core_options_ui_impl_fields_ParticipantFilterField<extended>
 */
class tubepress_test_core_options_ui_impl_fields_ParticipantFilterFieldTest extends tubepress_test_core_options_ui_impl_fields_AbstractMultiSelectFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionReference;

    /**
     * @var tubepress_core_options_ui_api_FieldProviderInterface[]
     */
    private $_mockOptionsPageParticipants;

    /**
     * @return tubepress_core_options_ui_impl_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        $sut = new tubepress_core_options_ui_impl_fields_ParticipantFilterField(

            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockTemplateFactory(),
            $this->_mockOptionReference
        );

        $sut->setOptionsPageParticipants($this->_mockOptionsPageParticipants);

        return $sut;
    }

    public function testGetId()
    {
        $result = $this->getSut()->getId();

        $this->assertEquals('participant-filter-field', $result);
    }

    public function testIsProOnly()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionReference = $this->mock(tubepress_core_options_api_ReferenceInterface::_);

        $this->_mockOptionReference->shouldReceive('getUntranslatedLabel')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('mock label');
        $this->_mockOptionReference->shouldReceive('getUntranslatedDescription')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('mock desc');

        $this->_mockOptionsPageParticipants = array();

        foreach (array('a', 'b', 'c', 'd') as $letter) {

            $participant = $this->mock('tubepress_core_options_ui_api_FieldProviderInterface');
            $participant->shouldReceive('getId')->andReturn($letter);
            $participant->shouldReceive('getTranslatedDisplayName')->andReturn(strtoupper($letter));
            $participant->shouldReceive('isAbleToBeFilteredFromGui')->andReturn($letter !== 'c');
            $this->_mockOptionsPageParticipants[] = $participant;
        }
    }

    protected function setupExpectationsForFailedStorageWhenAllMissing($errorMessage)
    {
        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'a;b;d')->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenAllMissing()
    {
        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'a;b;d')->andReturn(null);
    }

    /**
     * @return void
     */
    protected function doPrepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS)->andReturn('a;b;c');

        $mockTemplate->shouldReceive('setVariable')->once()->with('currentlySelectedValues', array('d'));
        $mockTemplate->shouldReceive('setVariable')->once()->with('ungroupedChoices', array('a' => 'A', 'b' => 'B', 'd' => 'D'));
        $mockTemplate->shouldReceive('setVariable')->once()->with('groupedChoices', array());
    }

    protected function setupExpectationsForFailedStorageWhenMixed($errorMessage)
    {
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('a', 'b'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'd')->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenMixed()
    {
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('a', 'b'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_OPTIONS_PAGE_PARTICIPANTS, 'd')->andReturn(null);
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'participant-filter-field';
    }
}

