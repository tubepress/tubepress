<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/DefaultTabsHandler.class.php';
require_once BASE . '/sys/classes/org/tubepress/spi/options/ui/Tab.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/util/StringUtils.class.php';

class org_tubepress_impl_template_templates_optionspage_TabsTemplateTest extends TubePressUnitTest {

    public function test()
    {
        $tab1 = \Mockery::mock(org_tubepress_spi_options_ui_Tab::_);
        $tab1->shouldReceive('getTitle')->times(3)->andReturn('title1');
        $tab1->shouldReceive('getHtml')->once()->andReturn('html1');

        $tab2 = \Mockery::mock(org_tubepress_spi_options_ui_Tab::_);
        $tab2->shouldReceive('getTitle')->times(3)->andReturn('title2');
        $tab2->shouldReceive('getHtml')->once()->andReturn('html2');

        ${org_tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} = array($tab1, $tab2);

        ob_start();
        include BASE . '/sys/ui/templates/options_page/tabs.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
        //$this->assertEquals($this->_removeNewLines($this->_expected()), $this->_removeNewLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<div id="tubepress_tabs" style="clear: both">

	<ul>

			<li>
			<a href="#tubepress_3f5468de0bfbe111586f7649a3c8d115">
				<span>title1</span>
			</a>
		</li>
			<li>
			<a href="#tubepress_5d1cb9970fd74ed9f56a867a785a358f">
				<span>title2</span>
			</a>
		</li>

	</ul>


		<div id="tubepress_3f5468de0bfbe111586f7649a3c8d115">

		   	html1		</div>


		<div id="tubepress_5d1cb9970fd74ed9f56a867a785a358f">

		   	html2		</div>


</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#tubepress_tabs").tabs();
	});
</script>

EOT;
    }

}