<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_template_templates_optionspage_fields_CheckboxFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME} = 'some-name';
        ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} = true;

        ob_start();
        include __DIR__ . '/../../../../../../main/resources/system-templates/options_page/fields/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(true)), tubepress_impl_util_StringUtils::removeEmptyLines($result));

        ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} = false;

        ob_start();
        include __DIR__ . '/../../../../../../main/resources/system-templates/options_page/fields/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(false)), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected($checked)
    {
        if ($checked) {

            return '<input type="checkbox" name="some-name" value="some-name" CHECKED />';
        }

        return '<input type="checkbox" name="some-name" value="some-name"  />';
    }

}