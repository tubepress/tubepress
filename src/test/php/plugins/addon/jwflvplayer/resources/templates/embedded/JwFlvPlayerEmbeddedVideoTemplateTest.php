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
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='99' height='88'>
    <param name='movie' value='tp-base-url/src/main/web/flash/longtail/player.swf'>
    <param name='allowfullscreen' value='true'>
    <param name='allowscriptaccess' value='always'>
    <param name='wmode' value='transparent'>
    <param name='flashvars' value='file=data-url&amp;autostart=starttt&amp;backcolor=back-color&amp;frontcolor=front-color&amp;lightcolor=light-color&amp;screencolor=screen-color'>

<embed	type='application/x-shockwave-flash'
        src='tp-base-url/src/main/web/flash/longtail/player.swf'
		width='99'
		height='88'
		bgcolor='undefined'
        allowscriptaccess='always'
        allowfullscreen='true'
        wmode='transparent'
        flashvars='file=data-url&amp;autostart=starttt&amp;backcolor=back-color&amp;frontcolor=front-color&amp;lightcolor=light-color&amp;screencolor=screen-color'
</embed>
</object>
EOT
);

        ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL} = 'tp-base-url';
        ${tubepress_api_const_template_Variable::VIDEO_ID} = 'video-id';
        ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH} = 99;
        ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT} = 88;
        ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL} = 'data-url';
        ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART} = 'starttt';
        ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_BACK} = 'back-color';
        ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_FRONT} = 'front-color';
        ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_LIGHT} = 'light-color';
        ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_SCREEN} = 'screen-color';

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/jwflvplayer/resources/templates/embedded/longtail.tpl.php';
    }
}