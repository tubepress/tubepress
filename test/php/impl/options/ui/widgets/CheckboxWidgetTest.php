<?php

require_once 'AbstractWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/BooleanInput.class.php';

class org_tubepress_impl_options_ui_widgets_CheckboxWidgetTest extends org_tubepress_impl_options_ui_widgets_AbstractWidgetTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_widgets_BooleanInput($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/widgets/checkbox.tpl.php';
    }
}

