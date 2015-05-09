<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener
 */
class tubepress_test_app_impl_listeners_template_pre_OptionsPageTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIncomingEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistence;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockIncomingEvent     = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockPersistence       = $this->mock(tubepress_app_api_options_PersistenceInterface::_);
        $this->_mockHttpRequestParams = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockTemplating        = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener(
            $this->_mockPersistence,
            $this->_mockHttpRequestParams,
            $this->_mockTemplating
        );
    }

    public function testMultiSource()
    {
        $mockField1 = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockField2 = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockField3 = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $mockField4 = $this->mock('tubepress_app_api_options_ui_FieldInterface');

        $templateVars = array(
            'categoryIdToProviderIdToFieldsMap' => array(
                tubepress_app_api_options_ui_CategoryNames::FEED => array(
                    'provider1' => array(
                        'fieldId1', 'fieldId2'
                    ),
                    'provider2' => array(
                        'fieldId3', 'fieldId4'
                    )
                ),
            ),
            'fields' => array(
                'field-1' => $mockField1,
                'field-2' => $mockField2,
                'field-3' => $mockField3,
                'field-4' => $mockField4
            )
        );

        $this->_sut->_applyMultiSource($templateVars);

        $this->assertTrue(true);
    }

    public function testMultiSourceNonArrayFields()
    {
        $templateVars = array(
            'categoryIdToProviderIdToFieldsMap' => array(
                tubepress_app_api_options_ui_CategoryNames::FEED => array(),
            ),
            'fields' => 'x',
        );

        $this->_sut->_applyMultiSource($templateVars);

        $this->assertTrue(true);
    }

    public function testMultiSourceNoFields()
    {
        $templateVars = array(
            'categoryIdToProviderIdToFieldsMap' => array(
                tubepress_app_api_options_ui_CategoryNames::FEED => array(),
            ),
        );

        $this->_sut->_applyMultiSource($templateVars);

        $this->assertTrue(true);
    }

    public function testMultiSourceNoFeedCategory()
    {
        $templateVars = array(
            'categoryIdToProviderIdToFieldsMap' => array(),
        );

        $this->_sut->_applyMultiSource($templateVars);

        $this->assertTrue(true);
    }

    public function testMultiSourceNoMapSet()
    {
        $templateVars = array();

        $this->_sut->_applyMultiSource($templateVars);

        $this->assertTrue(true);
    }
}