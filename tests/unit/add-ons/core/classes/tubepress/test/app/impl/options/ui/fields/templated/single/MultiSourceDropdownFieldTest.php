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
 * @covers tubepress_app_impl_options_ui_fields_templated_single_MultiSourceDropdownField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_single_MultiSourceDropdownFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_single_AbstractSingleOptionFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAcceptableValues;

    protected function onAfterSingleFieldSetup()
    {
        $this->_mockLangUtils        = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockAcceptableValues = $this->mock(tubepress_app_api_options_AcceptableValuesInterface::_);

        $this->onAfterDropDownFieldSetup();
    }

    /**
     * @return string
     */
    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/dropdown';
    }

    /**
     * @return array
     */
    protected function getAdditionalExpectedTemplateVariables()
    {
        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->once()->andReturn(true);

        $this->_mockAcceptableValues->shouldReceive('getAcceptableValues')->once()->with($this->getId())->andReturn(array(

            'foo' => 'abc', 'smack' => 'xyz'
        ));

        return array(
            'prefix' => 'abc-123-',
            'ungroupedChoices' => array('foo' => 'abc', 'smack' => 'xyz')
        );
    }

    protected function getMockLangUtils()
    {
        return $this->_mockLangUtils;
    }

    protected function getMockAcceptableValues()
    {
        return $this->_mockAcceptableValues;
    }

    /**
     * @return string
     */
    protected function getId()
    {
        return 'bla';
    }

    protected function onAfterDropDownFieldSetup()
    {
        //override point
    }

    protected function getMultiSourcePrefix()
    {
        return 'abc-123-';
    }

    /**
     * @return tubepress_app_impl_options_ui_fields_templated_AbstractTemplatedField
     */
    protected function getSut()
    {
        return new tubepress_app_impl_options_ui_fields_templated_single_MultiSourceDropdownField(

            $this->getId(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockOptionsReference(),
            $this->getMockTemplating(),
            $this->getMockLangUtils(),
            $this->getMockAcceptableValues(),
            'abc-123-'
        );
    }
}
