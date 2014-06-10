<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_youtube_resources_templates_embedded_YouTubeEmbeddedVideoTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function testTemplate()
    {
        $this->expectOutputString('<iframe id="some-dom-id" data-videoid="video-id" data-playerimplementation="some-embedded-impl-name" data-videoprovidername="some-video-provider" class="youtube-player" type="text/html" width="99" height="88" src="data-url" frameborder="0" allowfullscreen></iframe>');

        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_VIDEO_ID} = 'video-id';
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH} = 99;
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_HEIGHT} = 88;
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_DATA_URL} = 'data-url';
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_VIDEO_DOM_ID} = 'some-dom-id';
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME} = 'some-embedded-impl-name';
        ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_MEDIA_PROVIDER_NAME} = 'some-video-provider';

        require TUBEPRESS_ROOT . '/src/core/youtube/resources/templates/embedded/youtube.tpl.php';
    }
}