<?php

require_once BASE . '/sys/classes/org/tubepress/spi/options/ui/Widget.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/tabs/AbstractTab.class.php';

class org_tubepress_impl_template_templates_optionspage_TabTemplateTest extends TubePressUnitTest {

    public function test()
    {
        $one = \Mockery::mock(org_tubepress_spi_options_ui_Widget::_);
        $one->shouldReceive('getHtml')->once()->andReturn('one-html');
        $one->shouldReceive('getTitle')->once()->andReturn('one-title');
        $one->shouldReceive('getDescription')->once()->andReturn('one-description');
        $one->shouldReceive('isProOnly')->once()->andReturn(true);
        $one->shouldReceive('getArrayOfApplicableProviderNames')->once()->andReturn(array('foo', 'bar'));

        $two = \Mockery::mock(org_tubepress_spi_options_ui_Widget::_);
        $two->shouldReceive('getHtml')->once()->andReturn('two-html');
        $two->shouldReceive('getTitle')->once()->andReturn('two-title');
        $two->shouldReceive('getDescription')->once()->andReturn('two-description');
        $two->shouldReceive('isProOnly')->once()->andReturn(false);
        $two->shouldReceive('getArrayOfApplicableProviderNames')->once()->andReturn(array());

        ${org_tubepress_impl_options_ui_tabs_AbstractTab::TEMPLATE_VAR_WIDGETARRAY} = array($one, $two);

        ob_start();
        include BASE . '/sys/ui/templates/options_page/tab.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<table class="tubepress-tab">
    <tr class="tubepress-foo-option tubepress-bar-option tubepress-pro-option">
		<th>one-title</th>
		<td>
		    one-html			<br />
			one-description		</td>
	</tr>
    <tr class="">
		<th>two-title</th>
		<td>
		    two-html			<br />
			two-description		</td>
	</tr>
	</table>
EOT;
    }

}