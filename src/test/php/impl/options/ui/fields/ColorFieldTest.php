<?php

require_once 'AbstractOptionDescriptorBasedFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/ColorField.class.php';

class org_tubepress_impl_options_ui_fields_ColorFieldTest extends org_tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_fields_ColorField($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/fields/color.tpl.php';
    }
}

