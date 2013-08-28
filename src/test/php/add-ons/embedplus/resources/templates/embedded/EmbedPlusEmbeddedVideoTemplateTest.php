<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_embedplus_resources_templates_embedded_EmbedPlusEmbeddedVideoTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function testTemplate()
    {
        $this->expectOutputString(<<<EOT
<object id="video-dom-id" type="application/x-shockwave-flash" width="99" height="120" data="http://getembedplus.com/embedplus.swf">

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
        ${tubepress_api_const_template_Variable::VIDEO_DOM_ID} = 'video-dom-id';

        require TUBEPRESS_ROOT . '/src/main/php/add-ons/embedplus/resources/templates/embedded/embedplus.tpl.php';
    }
}