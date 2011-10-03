<?php

require_once 'AbstractOptionDescriptorBasedWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/ColorInput.class.php';

class org_tubepress_impl_options_ui_widgets_ColorWidgetTest extends org_tubepress_impl_options_ui_widgets_AbstractOptionDescriptorBasedWidgetTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_widgets_ColorInput($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/widgets/color.tpl.php';
    }
}

