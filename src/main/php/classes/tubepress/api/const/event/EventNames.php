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
     * This event is fired after TubePress loads all of the registered addons. If your add-on requires
     * any initialization, this is where you should run it.
     *
     * @subject null
     *
     * @api
     * @since 3.1.0
     */
    const BOOT_COMPLETE = 'tubepress.core.boot.complete';




    /**
     * This event is fired when TubePress generates inline CSS for the HTML <head>.
     *
     * @subject string The inline CSS.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_INLINE_CSS = 'tubepress.core.cssjs.inlineCss';

    /**
     * This event is fired when TubePress generates inline JS. This may show up in either the HTML
     * <head>, or lower in the DOM. It will be printed *before* the tubepress.js <script> tag.
     *
     * @subject string The inline JS.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_INLINE_JS = 'tubepress.core.cssjs.inlineJs';

    /**
     * This event is fired when TubePress builds the gallery initialization JS code.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                init code for the gallery in JavaScript.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GALLERY_INIT = 'tubepress.core.cssjs.galleryInit';

    /**
     * This event is fired when TubePress generates HTML <meta> tags for the HTML <head>.
     *
     * @subject string The HTML <meta> tags.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_META_TAGS = 'tubepress.core.cssjs.metaTags';

    /**
     * This event is fired when TubePress generates the HTML <script> tag for jQuery.
     *
     * @subject ehough_curly_Url The URL for jQuery.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_SCRIPT_URL_JQUERY = 'tubepress.core.cssjs.url.jQuery';

    /**
     * This event is fired when TubePress generates the HTML <script> tag for tubepress.js.
     *
     * @subject ehough_curly_Url The URL to tubepress.js.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_SCRIPT_URL_TUBEPRESSJS = 'tubepress.core.cssjs.url.tubePressJs';

    /**
     * This event is fired when TubePress generates the HTML <link> tag for tubepress.css.
     *
     * @subject ehough_curly_Url The URL to tubepress.css.
     */
    const CSS_JS_CSS_URL_TUBEPRESS = 'tubepress.core.cssjs.url.tubePressCss';

    /**
     * This event is fired when TubePress builds the TubePressJsConfig object.
     *
     * @subject array An associative array of name => values that will be converted into JSON and applied as
     *                global JS configuration for TubePress.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GLOBAL_JS_CONFIG = 'tubepress.core.cssjs.globalJsConfig';


    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject Exception The caught error message.
     *
     * @arg string 'message' The message to be displayed to the user. May contain HTML.
     *
     * @api
     * @since 3.1.0
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
     *
     * @api
     * @since 3.1.0
     */
    const HTML_EMBEDDED = 'tubepress.core.html.embedded';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject string The pagination HTML.
     *
     * @api
     * @since 3.1.0
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
     *
     * @api
     * @since 3.1.0
     */
    const HTML_PLAYERLOCATION = 'tubepress.core.html.playerLocation';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject string The HTML for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SEARCH_INPUT = 'tubepress.core.html.search.input';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject string The HTML for the single video.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SINGLE_VIDEO = 'tubepress.core.html.singleVideo';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject string The HTML for the thumbnail gallery.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_THUMBNAIL_GALLERY = 'tubepress.core.html.thumbnailGallery';




    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject string The HTTP body.
     *
     * @arg ehough_shortstop_api_HttpRequest  request  The HTTP request.
     * @arg ehough_shortstop_api_HttpResponse response The HTTP response.
     *
     * @api
     * @since 3.1.0
     */
    const HTTP_RESPONSE = 'tubepress.core.http.response';



    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.1.0
     */
    const OPTIONS_NVP_READFROMEXTERNAL = 'tubepress.core.options.nvp.readFromExternalInput';

    /**
     * This event is fired when an option descriptor is registered.
     *
     * @subject tubepress_spi_options_OptionDescriptor The option descriptor being registered.
     *
     * @api
     * @since 3.1.0
     */
    const OPTIONS_DESCRIPTOR_REGISTRATION = 'tubepress.core.options.descriptor.registration';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @arg string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.1.0
     */
    const OPTIONS_NVP_PREVALIDATIONSET = 'tubepress.core.options.nvp.preValidationSet';

    /**
     * This event is fired when TubePress retrieves the fields that will be displayed
     * on a tab.
     *
     * @subject array The array of fields for this tab.
     *
     * @arg tubepress_spi_options_ui_PluggableOptionsPageParticipant 'participant' The options page participant.
     * @arg string                                                   'tabName'     The name of the tab.
     *
     * @api
     * @since 3.1.0
     */
    const OPTIONS_UI_FIELDS_FOR_TAB = 'tubepress.core.options.ui.fieldsForTab';


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
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_EMBEDDED = 'tubepress.core.template.embedded';

    /**
     * This event is fired when TubePress generates the template for an options UI form.
     *
     * @subject ehough_contemplate_api_Template The options UI template.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_OPTIONS_UI_MAIN = 'tubepress.core.template.options.ui.main';

    /**
     * This event is fired when TubePress generates the template for the options UI tabs.
     *
     * @subject ehough_contemplate_api_Template The options UI tabs template.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_OPTIONS_UI_TABS_ALL = 'tubepress.core.template.options.ui.tabs.all';

    /**
     * This event is fired when TubePress generates the template for the options UI tabs.
     *
     * @subject ehough_contemplate_api_Template The options UI tabs template.
     *
     * @arg string 'tabName' The tab name.
     *
     * @api
     * @since 3.1.0
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
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_PLAYERLOCATION = 'tubepress.core.playerTemplateConstruction';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject ehough_contemplate_api_Template The template for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SEARCH_INPUT = 'tubepress.core.searchInputTemplateConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_Video 'video' The video to be played.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SINGLE_VIDEO = 'tubepress.core.singleVideoTemplateConstruction';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @arg tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @arg integer                              'page'             The page number.
     *
     * @api
     * @since 3.1.0
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
     *
     * @api
     * @since 3.1.0
     */
    const VIDEO_CONSTRUCTION = 'tubepress.core.videoConstruction';

    /**
     * This event is fired when a TubePress builds a tubepress_api_video_VideoGalleryPage.
     *
     * @subject tubepress_api_video_VideoGalleryPage The video gallery page being built.
     *
     * @api
     * @since 3.1.0
     */
    const VIDEO_GALLERY_PAGE = 'tubepress.core.videoGalleryPageConstruction';
}