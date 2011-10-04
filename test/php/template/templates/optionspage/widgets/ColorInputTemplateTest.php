<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/AbstractOptionDescriptorBasedWidget.class.php';

class org_tubepress_impl_template_templates_optionspage_widgets_ColorInputTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_options_ui_widgets_AbstractOptionDescriptorBasedWidget::TEMPLATE_VAR_NAME} = 'some-name';
        ${org_tubepress_impl_options_ui_widgets_AbstractOptionDescriptorBasedWidget::TEMPLATE_VAR_VALUE} = 'some-value';

        ob_start();
        include BASE . '/sys/ui/templates/options_page/widgets/color.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<input type="text" name="some-name" size="6" class="color" value="some-value" />
EOT;
    }

}