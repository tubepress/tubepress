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
class tubepress_plugins_embedplus_resources_templates_embedded_EmbedPlusEmbeddedVideoTemplateTest extends TubePressUnitTest
{
	public function testTemplate()
    {
        $this->expectOutputString(<<<EOT
<object type="application/x-shockwave-flash" width="99" height="120" data="http://getembedplus.com/embedplus.swf">

	<param name="movie"		value="http://getembedplus.com/embedplus.swf" />
	<param name="quality"		value="high" />
	<param name="wmode"		value="transparent" />
	<param name="allowscriptaccess"	value="always" />
	<param name="allowFullScreen"	value="true" />
	<param name="flashvars" 	value="ytid=video-id&width=99&height=88&hd=1" />

	<iframe class="cantembedplus" title="YouTube video player" width="99" height="88" src="data-url" frameborder="0" allowfullscreen></iframe>
</object>
<!--[if lte IE 6]> <style type="text/css">.cantembedplus{display:none;}</style><![endif]-->
EOT
);

        ${tubepress_api_const_template_Variable::VIDEO_ID} = 'video-id';
        ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH} = 99;
        ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT} = 88;
        ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL} = 'data-url';

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/embedplus/resources/templates/embedded/embedplus.tpl.php';
    }
}