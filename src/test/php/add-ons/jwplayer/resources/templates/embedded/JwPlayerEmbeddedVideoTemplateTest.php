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
class tubepress_test_addons_jwplayer_resources_templates_embedded_JwPlayerEmbeddedVideoTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function testTemplate()
    {
        $this->expectOutputString(<<<EOT
<div id="video-dom-id">
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
</div>
EOT
);

        ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL} = 'tp-base-url';
        ${tubepress_api_const_template_Variable::VIDEO_ID} = 'video-id';
        ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH} = 99;
        ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT} = 88;
        ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL} = 'data-url';
        ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART} = 'starttt';
        ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_BACK} = 'back-color';
        ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_FRONT} = 'front-color';
        ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_LIGHT} = 'light-color';
        ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_SCREEN} = 'screen-color';
        ${tubepress_api_const_template_Variable::VIDEO_DOM_ID} = 'video-dom-id';

        require TUBEPRESS_ROOT . '/src/main/php/add-ons/jwplayer/resources/templates/embedded/longtail.tpl.php';
    }
}