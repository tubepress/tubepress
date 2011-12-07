<?php

require_once 'AbstractOptionDescriptorBasedFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/DropdownField.class.php';

class org_tubepress_impl_options_ui_fields_DropdownFieldTest extends org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_fields_DropdownField($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/fields/dropdown.tpl.php';
    }

    protected function _performAdditionToStringTestSetup($template)
    {
        $od = $this->getOptionDescriptor();
        $od->shouldReceive('getAcceptableValues')->once()->andReturn(array('foo' => 'bar', 'smack' => 'rock'));
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_fields_DropdownField::TEMPLATE_VAR_ACCEPTABLE_VALUES,
            array('<<message: foo>>' => 'bar', '<<message: smack>>' => 'rock'));
    }
}

