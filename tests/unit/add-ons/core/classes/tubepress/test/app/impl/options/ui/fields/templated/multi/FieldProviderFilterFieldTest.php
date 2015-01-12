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
 * @covers tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_multi_FieldProviderFilterFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_multi_AbstractMultiSelectFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionReference;

    /**
     * @var tubepress_app_api_options_ui_FieldProviderInterface[]
     */
    private $_mockFieldProviders;

    /**
     * @return tubepress_app_impl_options_ui_fields_AbstractField
     */
    protected function getSut()
    {
        $sut = new tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField(

            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->_mockOptionReference
        );

        $sut->setFieldProviders($this->_mockFieldProviders);

        return $sut;
    }

    public function testGetId()
    {
        $result = $this->getSut()->getId();

        $this->assertEquals('provider_filter_field', $result);
    }

    public function testIsProOnly()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockOptionReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);

        $this->_mockOptionReference->shouldReceive('getUntranslatedLabel')->atLeast(1)->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS)->andReturn('mock label');
        $this->_mockOptionReference->shouldReceive('getUntranslatedDescription')->atLeast(1)->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS)->andReturn('mock desc');

        $this->_mockFieldProviders = array();

        foreach (array('a', 'b', 'c', 'd') as $letter) {

            $fieldProvider = $this->mock('tubepress_app_api_options_ui_FieldProviderInterface');
            $fieldProvider->shouldReceive('getId')->andReturn($letter);
            $fieldProvider->shouldReceive('getUntranslatedDisplayName')->andReturn(strtoupper($letter));
            $fieldProvider->shouldReceive('isAbleToBeFilteredFromGui')->andReturn($letter !== 'c');
            $this->_mockFieldProviders[] = $fieldProvider;
        }
    }

    protected function setupExpectationsForFailedStorageWhenAllMissing($errorMessage)
    {
        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS, 'a;b;d')->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenAllMissing()
    {
        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS, 'a;b;d')->andReturn(null);
    }

    /**
     * @return array
     */
    protected function getAdditionalExpectedTemplateVariables()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS)->andReturn('a;b;c');

        return array(
            'currentlySelectedValues' => array('d'),
            'ungroupedChoices' => array('a' => 'A', 'b' => 'B', 'd' => 'D'),
            'groupedChoices' => array(),
        );

    }

    protected function setupExpectationsForFailedStorageWhenMixed($errorMessage)
    {
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('a', 'b'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS, 'd')->andReturn($errorMessage);
    }

    protected function setupExpectationsForGoodStorageWhenMixed()
    {
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with($this->getOptionsPageItemId())->andReturn(array('a', 'b'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS, 'd')->andReturn(null);
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'provider_filter_field';
    }
}

