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

/**
 * A list of the "core" TubePress events.
 */
class tubepress_api_const_event_CoreEventNames
{
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
    const EMBEDDED_HTML_CONSTRUCTION = 'core.embeddedHtmlConstruction';

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
    const EMBEDDED_TEMPLATE_CONSTRUCTION = 'core.embeddedTemplateConstruction';

    /**
     * This event is fired when TubePress builds the gallery initialization JS code.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                init code for the gallery in JavaScript.
     *
     * @arg None
     */
    const GALLERY_INIT_JS_CONSTRUCTION = 'core.galleryInitJs';

    /**
     * This event is fired when TubePress builds *any* HTML. It is fired *after* any other
     * HTML-based events.
     *
     * @subject string The HTML.
     *
     * @arg None
     */
    const HTML_CONSTRUCTION = 'core.htmlConstruction';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject string The pagination HTML.
     *
     * @arg None
     */
    const PAGINATION_HTML_CONSTRUCTION = 'core.paginationHtmlConstruction';

    /**
     * This event is fired when a TubePress builds the HTML for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'      The video to be played.
     * @arg string                    'playerName' The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const PLAYER_HTML_CONSTRUCTION = 'core.playerHtmlConstruction';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'      The video to be played.
     * @arg string                    'playerName' The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const PLAYER_TEMPLATE_CONSTRUCTION = 'core.playerTemplateConstruction';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const PRE_VALIDATION_OPTION_SET = 'core.preValidationOptionSet';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject string The HTML for the search input.
     *
     * @arg None
     */
    const SEARCH_INPUT_HTML_CONSTRUCTION = 'core.searchInputHtmlConstruction';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject ehough_contemplate_api_Template The template for the search input.
     *
     * @arg None
     */
    const SEARCH_INPUT_TEMPLATE_CONSTRUCTION = 'core.searchInputTemplateConstruction';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject string The HTML for the single video.
     *
     * @arg None
     */
    const SINGLE_VIDEO_HTML_CONSTRUCTION = 'core.searchInputHtmlConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_Video 'video'        The video to be played.
     */
    const SINGLE_VIDEO_TEMPLATE_CONSTRUCTION = 'core.singleVideoTemplateConstruction';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject string The HTML for the thumbnail gallery.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const THUMBNAIL_GALLERY_HTML_CONSTRUCTION = 'core.thumbnailGalleryHtmlConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION = 'core.thumbnailGalleryTemplateConstruction';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const VARIABLE_READ_FROM_EXTERNAL_INPUT = 'core.variableReadFromExternalInput';

    /**
     * This event is fired when a TubePress builds a TubePress video. Some providers may add additional
     * arguments to this event.
     *
     * @subject tubepress_api_video_Video The TubePress video.
     *
     * @arg int   zeroBasedFeedIndex The zero-based index into the raw feed from which this video was built.
     * @arg mixed rawFeed            The "raw" unaltered feed from the provider.
     */
    const VIDEO_CONSTRUCTION = 'core.videoConstruction';

    /**
     * This event is fired when a TubePress builds a tubepress_api_video_VideoGalleryPage.
     *
     * @subject tubepress_api_video_VideoGalleryPage The video gallery page being built.
     */
    const VIDEO_GALLERY_PAGE_CONSTRUCTION = 'core.videoGalleryPageConstruction';
}