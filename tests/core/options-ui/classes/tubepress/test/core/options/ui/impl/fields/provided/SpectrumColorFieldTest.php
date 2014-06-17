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
 * @covers tubepress_core_options_ui_impl_fields_provided_SpectrumColorField<extended>
 */
class tubepress_test_core_options_ui_impl_fields_SpectrumColorFieldTest extends tubepress_test_core_options_ui_impl_fields_provided_AbstractProvidedOptionBasedFieldTest
{
    protected function buildSut()
    {
        return new tubepress_core_options_ui_impl_fields_provided_SpectrumColorField(

            $this->getOptionsPageItemId(),
            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockTemplateFactory(),
            $this->getMockOptionProvider()
        );
    }

    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/core/options-ui/resources/field-templates/spectrum-color.tpl.php';
    }

    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $this->getMockTranslator()->shouldReceive('_')->once()->with('cancel')->andReturn('yikes');
        $this->getMockTranslator()->shouldReceive('_')->once()->with('Choose')->andReturn('foo');

        $template->shouldReceive('setVariable')->once()->with('preferredFormat', 'hex');
        $template->shouldReceive('setVariable')->once()->with('showAlpha', false);
        $template->shouldReceive('setVariable')->once()->with('showInput', true);
        $template->shouldReceive('setVariable')->once()->with('showPalette', true);
        $template->shouldReceive('setVariable')->once()->with('cancelText', 'yikes');
        $template->shouldReceive('setVariable')->once()->with('chooseText', 'foo');

    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'abc';
    }
}
