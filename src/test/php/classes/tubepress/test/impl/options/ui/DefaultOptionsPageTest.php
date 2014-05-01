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
 * @covers tubepress_impl_options_ui_DefaultOptionsPage<extended>
 */
class tubepress_test_impl_options_ui_DefaultFormHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_DefaultOptionsPage
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockParticipants;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFields;

    public function onSetup()
    {
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEventDispatcher     = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockStorageManager      = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);

        $mockFieldA        = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageFieldInterface');
        $mockFieldB        = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageFieldInterface');
        $this->_mockFields = array($mockFieldA, $mockFieldB);

        $mockParticipant  = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface');
        $mockParticipant->shouldReceive('getFields')->once()->andReturn($this->_mockFields);
        $this->_mockParticipants = array($mockParticipant);

        $this->_sut = new tubepress_impl_options_ui_DefaultOptionsPage('some path', $this->_mockEnvironmentDetector);

        $this->_sut->setOptionsPageParticipants($this->_mockParticipants);
    }

    public function testSubmitWithErrors()
    {
        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->twice()->andReturn('field' . $index++);
            $mockField->shouldReceive('onSubmit')->once()->andReturn('yikes');
        }

        $this->_mockStorageManager->shouldReceive('flushSaveQueue')->once()->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array('field0' => 'yikes', 'field1' => 'yikes'), $result);
    }

    public function testSubmitNoErrors()
    {
        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->once()->andReturn('field' . $index++);
            $mockField->shouldReceive('onSubmit')->once()->andReturn(null);
        }

        $this->_mockStorageManager->shouldReceive('flushSaveQueue')->once()->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array(), $result);
    }

    public function testGetHTML()
    {
        $mockTemplate   = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockCategory   = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageItemInterface');
        $mockCategories = array($mockCategory);
        $mockMap        = array('categoryid' => array('field0', 'field1'));

        $mockCategory->shouldReceive('getId')->once()->andReturn('categoryid');

        $this->_mockParticipants[0]->shouldReceive('getCategories')->once()->andReturn($mockCategories);
        $this->_mockParticipants[0]->shouldReceive('getCategoryIdsToFieldIdsMap')->once()->andReturn($mockMap);
        $this->_mockParticipants[0]->shouldReceive('getId')->twice()->andReturn('participantid');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('some path')->andReturn($mockTemplate);

        $mockTemplate->shouldReceive('setVariable')->once()->with('errors', array('some error'));
        $mockTemplate->shouldReceive('setVariable')->once()->with('fields', ehough_mockery_Mockery::on(array($this, '__callbackVerifyFields')));
        $mockTemplate->shouldReceive('setVariable')->once()->with('categories', $mockCategories);
        $mockTemplate->shouldReceive('setVariable')->once()->with('isPro', true);
        $mockTemplate->shouldReceive('setVariable')->once()->with('justSubmitted', false);
        $mockTemplate->shouldReceive('setVariable')->once()->with('participants', ehough_mockery_Mockery::on(array($this, '__callbackVerifyFields')));
        $mockTemplate->shouldReceive('setVariable')->once()->with('tubePressBaseUrl', 'syz');
        $mockTemplate->shouldReceive('setVariable')->once()->with('successMessage', 'Settings updated.');
        $mockTemplate->shouldReceive('setVariable')->once()->with('categoryIdToParticipantIdToFieldsMap', array('categoryid' => array('participantid' => array('field0', 'field1'))));
        $mockTemplate->shouldReceive('setVariable')->once()->with('saveText', 'Save');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foobaz');

        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $mockBaseUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('syz');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->once()->andReturn('field' . $index++);
        }

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE,
                ehough_mockery_Mockery::on(array($this, '__callbackVerifyTemplateDispatch')));

        $this->assertEquals('foobaz', $this->_sut->getHTML(array('some error')));
    }

    public function __callbackVerifyFields($fields)
    {
        return is_array($fields);
    }

    public function __callbackVerifyTemplateDispatch($template)
    {
        return $template instanceof tubepress_api_event_EventInterface;
    }

}