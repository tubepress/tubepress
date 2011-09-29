<?php

require_once 'AbstractWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/DropdownInput.class.php';

class org_tubepress_impl_options_ui_widgets_DropdownWidgetTest extends org_tubepress_impl_options_ui_widgets_AbstractWidgetTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_widgets_DropdownInput($name);
    }

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/widgets/dropdown.tpl.php';
    }

    protected function _performAdditionToStringTestSetup($template)
    {
        $od = $this->getOptionDescriptor();
        $od->shouldReceive('getAcceptableValues')->once()->andReturn(array('foo' => 'bar', 'smack' => 'rock'));
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_widgets_DropdownInput::TEMPLATE_VAR_ACCEPTABLE_VALUES,
            array('<<message: foo>>' => 'bar', '<<message: smack>>' => 'rock'));
    }
}

