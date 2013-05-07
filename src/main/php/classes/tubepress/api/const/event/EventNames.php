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
class tubepress_api_const_event_EventNames
{
    /**
     * This event is fired after TubePress loads all of the registered addons.
     *
     * @subject null
     */
    const BOOT_COMPLETE = 'tubepress.core.boot.complete';




    /**
     * This event is fired when TubePress generates inline CSS.
     *
     * @subject string The inline CSS.
     */
    const CSS_JS_INLINE_CSS = 'tubepress.core.cssjs.inlineCss';

    /**
     * This event is fired when TubePress generates inline JS.
     *
     * @subject string The inline JS.
     */
    const CSS_JS_INLINE_JS = 'tubepress.core.cssjs.inlineJs';

    /**
     * This event is fired when TubePress builds the gallery initialization JS code.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                init code for the gallery in JavaScript.
     *
     * @arg None
     */
    const CSS_JS_GALLERY_INIT = 'tubepress.core.cssjs.galleryInit';

    /**
     * This event is fired when TubePress generates HTML <meta> tags.
     *
     * @subject string The HTML <meta> tags.
     */
    const CSS_JS_META_TAGS = 'tubepress.core.cssjs.metaTags';

    /**
     * This event is fired when TubePress generates the HTML <script> tag for jQuery.
     *
     * @subject string The HTML for the jQuery <script> tag.
     */
    const CSS_JS_SCRIPT_TAG_JQUERY = 'tubepress.core.cssjs.jQueryScriptTag';

    /**
     * This event is fired when TubePress generates the HTML <script> tag for tubepress.js.
     *
     * @subject string The HTML for the tubepress.js <script> tag.
     */
    const CSS_JS_SCRIPT_TAG_TUBEPRESS = 'tubepress.core.cssjs.tubePressScriptTag';

    /**
     * This event is fired when TubePress generates the HTML <link> tag for tubepress.css.
     *
     * @subject string The HTML for the tubepress.css <link> tag.
     */
    const CSS_JS_STYLESHEET_TAG_TUBEPRESS = 'tubepress.core.cssjs.tubePressStylesheetTag';

    /**
     * This event is fired when TubePress builds the TubePressJsConfig object.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                global JS configuration for TubePress.
     *
     * @arg None
     */
    const CSS_JS_GLOBAL_JS_CONFIG = 'tubepress.core.cssjs.globalJsConfig';


    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject Exception The caught error message.
     *
     * @arg string 'message' The message to be displayed to the user. May contain HTML.
     */
    const ERROR_EXCEPTION_CAUGHT = 'tubepress.core.error.exceptionCaught';




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
    const HTML_EMBEDDED = 'tubepress.core.html.embedded';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject string The pagination HTML.
     *
     * @arg None
     */
    const HTML_PAGINATION = 'tubepress.core.html.pagination';

    /**
     * This event is fired when a TubePress builds the HTML for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'      The video to be played.
     * @arg string                    'playerName' The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const HTML_PLAYERLOCATION = 'tubepress.core.html.playerLocation';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject string The HTML for the search input.
     *
     * @arg None
     */
    const HTML_SEARCH_INPUT = 'tubepress.core.html.search.input';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject string The HTML for the single video.
     *
     * @arg None
     */
    const HTML_SINGLE_VIDEO = 'tubepress.core.html.singleVideo';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject string The HTML for the thumbnail gallery.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const HTML_THUMBNAIL_GALLERY = 'tubepress.core.html.thumbnailGallery';




    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject string The HTTP body.
     *
     * @arg ehough_shortstop_api_HttpRequest  request  The HTTP request.
     * @arg ehough_shortstop_api_HttpResponse response The HTTP response.
     */
    const HTTP_RESPONSE = 'tubepress.core.http.response';



    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const OPTIONS_NVP_READFROMEXTERNAL = 'tubepress.core.options.nvp.readFromExternalInput';

    /**
     * This event is fired when an option descriptor is registered.
     *
     * @subject tubepress_spi_options_OptionDescriptor The option descriptor being registered.
     */
    const OPTIONS_DESCRIPTOR_REGISTRATION = 'tubepress.core.options.descriptor.registration';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     */
    const OPTIONS_NVP_PREVALIDATIONSET = 'tubepress.core.options.nvp.preValidationSet';




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
    const TEMPLATE_EMBEDDED = 'tubepress.core.template.embedded';

    /**
     * This event is fired when TubePress generates the template for an options UI form.
     *
     * @subject ehough_contemplate_api_Template The options UI template.
     */
    const TEMPLATE_OPTIONS_UI_MAIN = 'tubepress.core.template.options.ui.main';

    /**
     * This event is fired when TubePress generates the template for the options UI tabs.
     *
     * @subject ehough_contemplate_api_Template The options UI tabs template.
     */
    const TEMPLATE_OPTIONS_UI_TABS_ALL = 'tubepress.core.template.options.ui.tabs.all';

    /**
     * This event is fired when TubePress generates the template for the options UI tabs.
     *
     * @subject ehough_contemplate_api_Template The options UI tabs template.
     *
     * @arg string 'tabName' The tab name.
     */
    const TEMPLATE_OPTIONS_UI_TABS_SINGLE = 'tubepress.core.template.options.ui.tabs.single';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @arg tubepress_api_video_Video 'video'      The video to be played.
     * @arg string                    'playerName' The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     */
    const TEMPLATE_PLAYERLOCATION = 'tubepress.core.playerTemplateConstruction';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject ehough_contemplate_api_Template The template for the search input.
     *
     * @arg None
     */
    const TEMPLATE_SEARCH_INPUT = 'tubepress.core.searchInputTemplateConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_Video 'video' The video to be played.
     */
    const TEMPLATE_SINGLE_VIDEO = 'tubepress.core.singleVideoTemplateConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     */
    const TEMPLATE_THUMBNAIL_GALLERY = 'tubepress.core.thumbnailGalleryTemplateConstruction';




    /**
     * This event is fired when a TubePress builds a TubePress video. Some providers may add additional
     * arguments to this event.
     *
     * @subject tubepress_api_video_Video The TubePress video.
     *
     * @arg int   zeroBasedFeedIndex The zero-based index into the raw feed from which this video was built.
     * @arg mixed rawFeed            The "raw" unaltered feed from the provider.
     */
    const VIDEO_CONSTRUCTION = 'tubepress.core.videoConstruction';

    /**
     * This event is fired when a TubePress builds a tubepress_api_video_VideoGalleryPage.
     *
     * @subject tubepress_api_video_VideoGalleryPage The video gallery page being built.
     */
    const VIDEO_GALLERY_PAGE = 'tubepress.core.videoGalleryPageConstruction';
}