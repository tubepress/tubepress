<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License => v. 2.0. If a copy of the MPL was not distributed with this
 * file => You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_options_ui_fields_templated_single_SpectrumColorField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_single_SpectrumColorFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_single_AbstractSingleOptionFieldTest
{
    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/spectrum-color';
    }

    protected function getAdditionalExpectedTemplateVariables()
    {
        return array(
            
            'preferredFormat' => 'hex',
            'showAlpha' => false,
            'showInput' => true,
            'showPalette' => true,
            'cancelText' => 'cancel',
            'chooseText' => 'Choose',
        );
    }

    /**
     * @return string
     */
    protected function getId()
    {
        return 'abc';
    }

    /**
     * @return tubepress_app_impl_options_ui_fields_templated_AbstractTemplatedField
     */
    protected function getSut()
    {
        return new tubepress_app_impl_options_ui_fields_templated_single_SpectrumColorField(

            $this->getId(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->getMockOptionsReference()
        );
    }

    protected function getMultiSourcePrefix()
    {
        return '';
    }
}
