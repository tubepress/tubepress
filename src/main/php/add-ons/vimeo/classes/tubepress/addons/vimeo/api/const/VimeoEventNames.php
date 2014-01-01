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
 * Vimeo-specific event names.
 */
class tubepress_addons_vimeo_api_const_VimeoEventNames
{
    /**
     * This event is fired after TubePress builds the URL to fetch a set of videos
     * from Vimeo.
     *
     * @subject ehough_curly_Url The Vimeo API URL.
     */
    const URL_GALLERY = 'tubepress.core.vimeo.url.gallery';

    /**
     * This event is fired after TubePress builds the URL to fetch a single video
     * from Vimeo.
     *
     * @subject ehough_curly_Url The Vimeo API URL.
     */
    const URL_SINGLE = 'tubepress.core.vimeo.url.single';
}
