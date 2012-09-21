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
     * This event is fired when TubePress builds the PHP/HTML template for an embedded
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

    /**
     * This event is fired when TubePress builds the gallery initialization JS code.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                init code for the gallery in JavaScript.
     *
     * @arg None
     */
    const GALLERY_INIT_JS_CONSTRUCTION = 'galleryInitJs';

    /**
     * This event is fired when TubePress builds *any* HTML. It is fired *after* any other
     * HTML-based events.
     *
     * @subject string The HTML.
     *
     * @arg None
     */
    const HTML_CONSTRUCTION = 'htmlConstruction';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject string The pagination HTML.
     *
     * @arg None
     */
    const PAGINATION_HTML_CONSTRUCTION = 'paginationHtmlConstruction';

    /**
     * This event is fired when a TubePress builds the HTML for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'        The video to be played.
     * @arg string                    'providerName' The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg string                    'playerName'   The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const PLAYER_HTML_CONSTRUCTION = 'playerHtmlConstruction';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'        The video to be played.
     * @arg string                    'providerName' The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg string                    'playerName'   The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const PLAYER_TEMPLATE_CONSTRUCTION = 'playerTemplateConstruction';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const PRE_VALIDATION_OPTION_SET = 'preValidationOptionSet';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject string The HTML for the search input.
     *
     * @arg None
     */
    const SEARCH_INPUT_HTML_CONSTRUCTION = 'searchInputHtmlConstruction';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject ehough_contemplate_api_Template The template for the search input.
     *
     * @arg None
     */
    const SEARCH_INPUT_TEMPLATE_CONSTRUCTION = 'searchInputTemplateConstruction';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject string The HTML for the single video.
     *
     * @arg None
     */
    const SINGLE_VIDEO_HTML_CONSTRUCTION = 'searchInputHtmlConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg string                    'providerName' The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg tubepress_api_video_Video 'video'        The video to be played.
     */
    const SINGLE_VIDEO_TEMPLATE_CONSTRUCTION = 'singleVideoTemplateConstruction';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject string The HTML for the thumbnail gallery.
     *
     * @arg string                               'providerName'     The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const THUMBNAIL_GALLERY_HTML_CONSTRUCTION = 'thumbnailGalleryHtmlConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg string                               'providerName'     The name of the video provider (e.g. "vimeo" or "youtube").
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION = 'thumbnailGalleryTemplateConstruction';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const VARIABLE_READ_FROM_EXTERNAL_INPUT = 'variableReadFromExternalInput';

    /**
     * This event is fired when a TubePress builds a TubePress video.
     *
     * @subject tubepress_api_video_Video The TubePress video.
     *
     * @arg string 'providerName' The name of the video provider (e.g. "vimeo" or "youtube").
     */
    const VIDEO_CONSTRUCTION = 'core.videoConstruction';
}