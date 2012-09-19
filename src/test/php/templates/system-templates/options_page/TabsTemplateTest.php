<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_template_templates_optionspage_TabsTemplateTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $tab1 = \Mockery::mock(tubepress_spi_options_ui_Tab::_);
        $tab1->shouldReceive('getTitle')->times(3)->andReturn('title1');
        $tab1->shouldReceive('getHtml')->once()->andReturn('html1');

        $tab2 = \Mockery::mock(tubepress_spi_options_ui_Tab::_);
        $tab2->shouldReceive('getTitle')->times(3)->andReturn('title2');
        $tab2->shouldReceive('getHtml')->once()->andReturn('html2');

        ${tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} = array($tab1, $tab2);

        ob_start();
        include __DIR__ . '/../../../../../main/resources/system-templates/options_page/tabs.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
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