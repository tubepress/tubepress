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
 * @covers tubepress_impl_options_ui_fields_TextField<extended>
 */
class tubepress_test_impl_options_ui_fields_TextFieldTest extends tubepress_test_impl_options_ui_fields_AbstractProvidedOptionBasedFieldTest
{
    public function testInvalidSize()
    {
        $this->setExpectedException('InvalidArgumentException', 'Text fields must have a non-negative size.');

        /**
         * @var $field tubepress_impl_options_ui_fields_TextField
         */
        $field = $this->getSut();

        $field->setSize(-1);
    }

    protected function buildSut()
    {
        $field = new tubepress_impl_options_ui_fields_TextField($this->getOptionName(), $this->getMockMessageService());

        $field->setSize(99);

        return $field;
    }

    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/text.tpl.php';
    }

    protected function doAdditionalPrepForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        $template->shouldReceive('setVariable')->once()->with('size', 99);
    }
}
