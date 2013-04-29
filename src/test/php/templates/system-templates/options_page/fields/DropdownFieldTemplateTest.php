<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_template_templates_optionspage_fields_DropdownFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME} = 'some-name';
        ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} = 'poo';
        ${tubepress_impl_options_ui_fields_DropdownField::TEMPLATE_VAR_ACCEPTABLE_VALUES} = array('crack' => 'rock', 'poo' => 'some-value', 'pretzels' => 'jets');

        ob_start();
        include __DIR__ . '/../../../../../../main/resources/system-templates/options_page/fields/dropdown.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<select name="some-name">
	<option value="crack" >rock</option>
	<option value="poo" SELECTED>some-value</option>
	<option value="pretzels" >jets</option>
</select>
EOT;
    }

}