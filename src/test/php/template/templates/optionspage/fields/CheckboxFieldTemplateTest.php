<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/AbstractOptionDescriptorBasedField.class.php';

class org_tubepress_impl_template_templates_optionspage_fields_CheckboxFieldTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME} = 'some-name';
        ${org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} = true;

        ob_start();
        include BASE . '/sys/ui/templates/options_page/fields/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(true)), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));

        ${org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} = false;

        ob_start();
        include BASE . '/sys/ui/templates/options_page/fields/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(false)), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected($checked)
    {
        if ($checked) {

            return '<input type="checkbox" name="some-name" value="some-name" CHECKED />';
        }

        return '<input type="checkbox" name="some-name" value="some-name"  />';
    }

}