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
 * A list of the "core" TubePress events.
 */
class tubepress_api_const_event_CoreEventNames
{
    /**
     * This event is fired after TubePress has completed its initialization.
     *
     * @subject None
     * @arg     None
     */
    const BOOT = 'boot';

    /**
     * This event is fired when TubePress builds the HTML for an embedded video player.
     *
     * @subject string The HTML for the embedded video player.
     *
     * @arg string           'videoId'                    The ID of the video to be played.
     * @arg string           'providerName'               The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg ehough_curly_Url 'dataUrl'                    The embedded data URL.
     * @arg string           'embeddedImplementationName' The name of the embedded implementation.
     */
    const EMBEDDED_HTML_CONSTRUCTION = 'embeddedHtmlConstruction';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for an embedded
     * video player.
     *
     * @subject ehough_contemplate_api_Template The embedded video player template.
     *
     * @arg string           'videoId'                    The ID of the video to be played.
     * @arg string           'providerName'               The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg ehough_curly_Url 'dataUrl'                    The embedded data URL.
     * @arg string           'embeddedImplementationName' The name of the embedded implementation.
     */
    const EMBEDDED_TEMPLATE_CONSTRUCTION = 'embeddedTemplateConstruction';
}