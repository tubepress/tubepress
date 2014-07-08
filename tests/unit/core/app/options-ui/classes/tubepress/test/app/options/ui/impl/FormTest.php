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
 * @covers tubepress_app_options_ui_impl_Form<extended>
 */
class tubepress_test_app_options_ui_impl_FormTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ui_impl_Form
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFieldProviders;

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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    public function onSetup()
    {
        $this->_mockEventDispatcher     = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);
        $this->_mockStorageManager      = $this->mock(tubepress_app_options_api_PersistenceInterface::_);
        $this->_mockTemplate            = $this->mock(tubepress_lib_template_api_TemplateInterface::_);
        $this->_mockStringUtils         = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);

        $mockFieldA        = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $mockFieldB        = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $this->_mockFields = array($mockFieldA, $mockFieldB);

        $mockFieldProvider  = $this->mock('tubepress_app_options_ui_api_FieldProviderInterface');
        $mockFieldProvider->shouldReceive('getFields')->once()->andReturn($this->_mockFields);
        $this->_mockFieldProviders = array($mockFieldProvider);

        $this->_sut = new tubepress_app_options_ui_impl_Form(

            $this->_mockTemplate,
            $this->_mockEnvironmentDetector,
            $this->_mockStorageManager,
            $this->_mockEventDispatcher,
            $this->_mockStringUtils
        );

        $this->_sut->setFieldProviders($this->_mockFieldProviders);
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
        $mockCategory   = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $mockCategories = array($mockCategory);
        $mockMap        = array('categoryid' => array('field0', 'field1'));

        $mockCategory->shouldReceive('getId')->once()->andReturn('categoryid');

        $this->_mockFieldProviders[0]->shouldReceive('getCategories')->once()->andReturn($mockCategories);
        $this->_mockFieldProviders[0]->shouldReceive('getCategoryIdsToFieldIdsMap')->once()->andReturn($mockMap);
        $this->_mockFieldProviders[0]->shouldReceive('getId')->twice()->andReturn('providerId');

        $ctx = array(
            'categories' => $mockCategories,
            'categoryIdToProviderIdToFieldsMap' => array('categoryid' => array('providerId' => array('field0' => 'field1'))),
            'errors' => array('some error'),
            'fields' => $this->_mockFields,
            'isPro' => true,
            'justSubmitted' => false,
            'fieldProviders' => $this->_mockFieldProviders,
            'successMessage' => 'Settings updated.',
            'tubePressBaseUrl' => 'syz',
            'saveText' => 'Save'
        );
        $this->_mockTemplate->shouldReceive('setVariables')->once()->with(ehough_mockery_Mockery::any());
        $this->_mockTemplate->shouldReceive('toString')->once()->andReturn('foobaz');

        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('syz');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $index = 0;

        foreach ($this->_mockFields as $mockField) {

            $mockField->shouldReceive('getId')->once()->andReturn('field' . $index++);
        }

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockTemplate)->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE, $mockEvent);

        $this->assertEquals('foobaz', $this->_sut->getHTML(array('some error')));
    }

    public function __callbackVerifyFields($fields)
    {
        return is_array($fields);
    }
}