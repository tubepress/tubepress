<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_plugins_youtube_resources_templates_embedded_YouTubeEmbeddedVideoTemplateTest extends TubePressUnitTest
{
	public function testTemplate()
    {
        $this->expectOutputString('<iframe id="tubepress-youtube-player-video-id" class="youtube-player" type="text/html" width="99" height="88" src="data-url" frameborder="0"></iframe>');

        ${tubepress_api_const_template_Variable::VIDEO_ID} = 'video-id';
        ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH} = 99;
        ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT} = 88;
        ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL} = 'data-url';

        require TUBEPRESS_ROOT . '/src/main/php/plugins/youtube/resources/templates/embedded/youtube.tpl.php';
    }
}