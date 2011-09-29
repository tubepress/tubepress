<?php

require_once 'AbstractWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/TextInput.class.php';

class org_tubepress_impl_options_ui_widgets_TextWidgetTest extends org_tubepress_impl_options_ui_widgets_AbstractWidgetTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_widgets_TextInput($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/widgets/text.tpl.php';
    }
}

