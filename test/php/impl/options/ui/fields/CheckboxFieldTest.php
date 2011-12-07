<?php

require_once 'AbstractOptionDescriptorBasedFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/BooleanField.class.php';

class org_tubepress_impl_options_ui_fields_CheckboxFieldTest extends org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_fields_BooleanField($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/fields/checkbox.tpl.php';
    }
}

