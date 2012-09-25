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
class org_tubepress_impl_template_templates_optionspage_fields_MultiSelectTemplateTest extends TubePressUnitTest
{
    public function test()
    {
        $one   = new tubepress_api_model_options_OptionDescriptor('name-one');
        $two   = new tubepress_api_model_options_OptionDescriptor('name-two');
        $three = new tubepress_api_model_options_OptionDescriptor('name-three');

        $one->setLabel('label-one');
        $two->setLabel('label-two');
        $three->setLabel('label-three');

        $descriptors = array($one, $two, $three);

        ${tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_NAME} = 'some-name';
        ${tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_DESCRIPTORS} = $descriptors;
        ${tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_CURRENTVALUES} = array('crack', 'name-one', 'pittsburgh', 'steelers', 'name-three');

        ob_start();
        include __DIR__ . '/../../../../../../main/resources/system-templates/options_page/fields/multiselect.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<select id="multiselect-some-name" name="some-name[]" multiple="multiple">
	<option value="name-one" selected="selected">label-one</option>
	<option value="name-two" >label-two</option>
	<option value="name-three" selected="selected">label-three</option>
	</select>
EOT;
    }

}