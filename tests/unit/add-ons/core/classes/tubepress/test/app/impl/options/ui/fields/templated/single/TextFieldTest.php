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
 * @covers tubepress_app_impl_options_ui_fields_templated_single_TextField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_templated_single_TextField extends tubepress_test_app_impl_options_ui_fields_templated_single_AbstractSingleOptionFieldTest
{
    public function testInvalidSize()
    {
        $this->setExpectedException('InvalidArgumentException', 'Text fields must have a non-negative size.');

        /**
         * @var $field tubepress_app_impl_options_ui_fields_templated_single_TextField
         */
        $field = $this->getSut();

        $field->setSize(-1);
    }

    protected function getSut()
    {
        $field = new tubepress_app_impl_options_ui_fields_templated_single_TextField(

            $this->getOptionsPageItemId(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->getMockOptionsReference()
        );

        $field->setSize(99);

        return $field;
    }

    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/text';
    }

    protected function getAdditionalExpectedTemplateVariables()
    {
        return array('size' => 99);
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return 'foo';
    }
}
