<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/AbstractWidget.class.php';

class org_tubepress_impl_template_templates_optionspage_widgets_TextInputTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_options_ui_widgets_AbstractWidget::TEMPLATE_VAR_NAME} = 'some-name';
        ${org_tubepress_impl_options_ui_widgets_AbstractWidget::TEMPLATE_VAR_VALUE} = 'some-value';

        ob_start();
        include BASE . '/sys/ui/templates/options_page/widgets/text.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<input type="text" name="some-name" size="20" value="some-value" />
EOT;
    }

}