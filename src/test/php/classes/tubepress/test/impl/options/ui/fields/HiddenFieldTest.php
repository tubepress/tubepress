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
 * @covers tubepress_impl_options_ui_fields_HiddenField<extended>
 */
class tubepress_test_impl_options_ui_fields_HiddenFieldTest extends tubepress_test_impl_options_ui_fields_AbstractProvidedOptionBasedFieldTest
{
    protected function buildSut()
    {
        return new tubepress_impl_options_ui_fields_HiddenField($this->getOptionName());
    }

    protected function getExpectedTemplatePath()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/hidden.tpl.php';
    }
}
