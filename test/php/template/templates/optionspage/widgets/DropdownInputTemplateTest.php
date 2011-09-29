<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/AbstractWidget.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/DropdownInput.class.php';

class org_tubepress_impl_template_templates_optionspage_widgets_DropdownInputTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_options_ui_widgets_AbstractWidget::TEMPLATE_VAR_NAME} = 'some-name';
        ${org_tubepress_impl_options_ui_widgets_AbstractWidget::TEMPLATE_VAR_VALUE} = 'some-value';
        ${org_tubepress_impl_options_ui_widgets_DropdownInput::TEMPLATE_VAR_ACCEPTABLE_VALUES} = array('crack' => 'rock', 'poo' => 'some-value', 'pretzels' => 'jets');

        ob_start();
        include BASE . '/sys/ui/templates/options_page/widgets/dropdown.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<select name="some-name">
	<option value="crack" >rock</option>
	<option value="poo" SELECTED>some-value</option>
	<option value="pretzels" >jets</option>
</select>
EOT;
    }

}