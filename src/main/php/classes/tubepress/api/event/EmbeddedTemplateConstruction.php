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

/**
 * This event is fired when a TubePress builds the PHP/HTML template for a TubePress embedded
 * video player.
 */
class tubepress_api_event_EmbeddedTemplateConstruction extends ehough_tickertape_impl_GenericEvent
{
    const EVENT_NAME = 'tubepress.api.event.EmbeddedTemplateConstruction';

    /**
     * The ID of the video to be played.
     */
    const ARGUMENT_VIDEO_ID = 'videoId';

    /**
     * The name of the video provider (e.g. "vimeo" or "youtube").
     */
    const ARGUMENT_PROVIDER_NAME = 'providerName';

    /**
     * The embedded data URL.
     */
    const ARGUMENT_DATA_URL = 'dataUrl';

    /**
     * The embedded implementation name.
     */
    const ARGUMENT_EMBEDDED_IMPLEMENTATION_NAME = 'embeddedImplementationName';
}
