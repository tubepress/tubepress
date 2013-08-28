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
class tubepress_impl_template_templates_optionspage_fields_MultiSelectTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        $one   = new tubepress_spi_options_OptionDescriptor('name-one');
        $two   = new tubepress_spi_options_OptionDescriptor('name-two');
        $three = new tubepress_spi_options_OptionDescriptor('name-three');

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