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
class tubepress_plugins_jwflvplayer_resources_templates_embedded_JwFlvPlayerEmbeddedVideoTemplateTest extends TubePressUnitTest
{
	public function testTemplate()
    {
        $this->expectOutputString(<<<EOT
<embed	src="tp-base-url/sys/ui/static/flash/longtail/player.swf"
		width="99"
		height="88"
        allowscriptaccess="never"
        wmode="opaque"
        movie="tp-base-url/sys/ui/static/flash/longtail/player.swf"
        bgcolor="some-color"
        frontcolor="some-color"
        quality="high"
        flashvars="file=data-url&amp;autostart=starttt&amp;height=88&amp;width=99&amp;frontcolor=some-color"
</embed>
EOT
);

        ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL} = 'tp-base-url';
        ${tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY} = 'some-color';
        ${tubepress_api_const_template_Variable::VIDEO_ID} = 'video-id';
        ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH} = 99;
        ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT} = 88;
        ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL} = 'data-url';
        ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART} = 'starttt';

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/jwflvplayer/resources/templates/embedded/longtail.tpl.php';
    }
}