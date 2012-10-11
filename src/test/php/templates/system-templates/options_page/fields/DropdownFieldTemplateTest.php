<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_template_templates_optionspage_fields_DropdownFieldTemplateTest extends TubePressUnitTest
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