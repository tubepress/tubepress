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
class tubepress_impl_template_templates_optionspage_TabTemplateTest extends TubePressUnitTest
{
    public function test()
    {
        $one = \Mockery::mock(tubepress_spi_options_ui_Field::_);
        $one->shouldReceive('getHtml')->once()->andReturn('one-html');
        $one->shouldReceive('getTitle')->once()->andReturn('one-title');
        $one->shouldReceive('getDescription')->once()->andReturn('one-description');
        $one->shouldReceive('isProOnly')->once()->andReturn(true);

        $two = \Mockery::mock(tubepress_spi_options_ui_Field::_);
        $two->shouldReceive('getHtml')->once()->andReturn('two-html');
        $two->shouldReceive('getTitle')->once()->andReturn('two-title');
        $two->shouldReceive('getDescription')->once()->andReturn('two-description');
        $two->shouldReceive('isProOnly')->once()->andReturn(false);

        $mockParticipant = Mockery::mock(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
        $mockParticipant->shouldReceive('getName')->times(3)->andReturn('popp-name');
        $mockParticipant->shouldReceive('getFriendlyName')->once()->andReturn('friendly-popp-name');
        $mockParticipant->shouldReceive('getFieldsForTab')->once()->with('some-crazy-tab')->andReturn(array($one, $two));

        ${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_PARTICIPANT_ARRAY} = array($mockParticipant);
        ${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_TAB_NAME} = 'some-crazy-tab';
        ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL} = 'tubepress-base-url';

        ob_start();
        include __DIR__ . '/../../../../../main/resources/system-templates/options_page/tab.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<div class="tubepress-tab">
    <div class="ui-corner-all ui-widget-content tubepress-participant tubepress-participant-popp-name">
        <div class="ui-widget ui-widget-header tubepress-participant-header">
            <span>friendly-popp-name</span>
        </div>
    <table>
    <tr>
		<th class="tubepress-field-header"><a href="http://tubepress.org/pro"><img src="tubepress-base-url/src/main/web/images/pro_tag.png" alt="TubePress Pro only" /></a><span>one-title</span></th>
		<td>
		    one-html			<br />
			one-description		</td>
	</tr>
    <tr>
		<th class="tubepress-field-header"><span>two-title</span></th>
		<td>
		    two-html			<br />
			two-description		</td>
	</tr>
    </table></div>
</div>
EOT;
    }

}