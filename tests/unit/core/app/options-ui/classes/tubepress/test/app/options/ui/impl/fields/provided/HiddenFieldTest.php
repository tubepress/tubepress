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
 * @covers tubepress_app_options_ui_impl_fields_provided_HiddenField<extended>
 */
class tubepress_test_app_options_ui_impl_fields_HiddenFieldTest extends tubepress_test_app_options_ui_impl_fields_provided_AbstractProvidedOptionBasedFieldTest
{
    protected function buildSut()
    {
        return new tubepress_app_options_ui_impl_fields_provided_HiddenField(

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
        return TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/hidden.tpl.php';
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'foo';
    }
}
