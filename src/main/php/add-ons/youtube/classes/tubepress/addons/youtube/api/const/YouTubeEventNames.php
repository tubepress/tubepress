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

/**
 * YouTube-specific event names.
 */
class tubepress_addons_youtube_api_const_YouTubeEventNames
{
    /**
     * This event is fired after TubePress builds the URL to fetch a set of videos
     * from YouTube.
     *
     * @subject ehough_curly_Url The Vimeo API URL.
     */
    const URL_GALLERY = 'tubepress.core.youtube.url.gallery';

    /**
     * This event is fired after TubePress builds the URL to fetch a single video
     * from YouTube.
     *
     * @subject ehough_curly_Url The Vimeo API URL.
     */
    const URL_SINGLE = 'tubepress.core.youtube.url.single';
}
