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
 * @covers tubepress_app_options_ui_impl_fields_provided_DropdownField<extended>
 */
class tubepress_test_app_options_ui_impl_fields_provided_DropdownFieldTest extends tubepress_test_app_options_ui_impl_fields_provided_AbstractProvidedOptionBasedFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAcceptableValues;

    protected function onAfterProvidedFieldSetup()
    {
        $this->_mockLangUtils        = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockAcceptableValues = $this->mock(tubepress_app_options_api_AcceptableValuesInterface::_);

        $this->onAfterDropDownFieldSetup();
    }

    protected function buildSut()
    {
        return new tubepress_app_options_ui_impl_fields_provided_DropdownField(

            $this->getOptionsPageItemId(),
            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockOptionProvider(),
            $this->getMockTemplateFactory(),
            $this->_mockLangUtils,
            $this->_mockAcceptableValues
        );
    }

    /**
     * @return string
     */
    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/dropdown.tpl.php';
    }

    /**
     * @return void
     */
    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->once()->andReturn(true);

        $this->_mockAcceptableValues->shouldReceive('getAcceptableValues')->once()->with($this->getOptionsPageItemId())->andReturn(array(

            'foo' => 'bar', 'smack' => 'rock'
        ));

        $this->getMockTranslator()->shouldReceive('_')->once()->with('bar')->andReturn('abc');
        $this->getMockTranslator()->shouldReceive('_')->once()->with('rock')->andReturn('xyz');

        $template->shouldReceive('setVariable')->once()->with('choices',
            array('foo' => 'abc', 'smack' => 'xyz'));
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
    protected function getOptionsPageItemId()
    {
        return 'bla';
    }

    protected function onAfterDropDownFieldSetup()
    {
        //override point
    }
}
