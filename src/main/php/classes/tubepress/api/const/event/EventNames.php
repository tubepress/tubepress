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
 * A list of the "core" TubePress events.
 *
 * @package TubePress\Const\Event
 */
class tubepress_api_const_event_EventNames
{
    /**
     * This event is fired when TubePress is about to print its stylesheets to the HTML head.
     *
     * @subject An associative `array` where keys are style handles and values are style details.
     *          See {@link tubepress_spi_html_CssAndJsRegistryInterface::getStyle()} for details on array values.
     *
     * @api
     * @since 3.1.3
     */
    const CSS_JS_STYLESHEETS = 'tubepress.core.cssjs.stylesheets';

    /**
     * This event is fired when TubePress is about to print its JS files (either in <head> or near </body>).
     *
     * @subject An associative `array` where keys are script handles and values are script details.
     *          See {@link tubepress_spi_html_CssAndJsRegistryInterface::getScript()} for details on array values.
     *
     * @api
     * @since 3.1.3
     */
    const CSS_JS_SCRIPTS = 'tubepress.core.cssjs.scripts';

    /**
     * This event is fired when TubePress builds the gallery initialization JS code.
     *
     * @subject An associative `array` that will be converted into JSON and applied as
     *          init code for the gallery in JavaScript.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GALLERY_INIT = 'tubepress.core.cssjs.galleryInit';

    /**
     * This event is fired when TubePress builds the `TubePressJsConfig` object.
     *
     * @subject An associative `array` that will be converted into JSON and applied as global JS configuration
     *          for TubePress.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GLOBAL_JS_CONFIG = 'tubepress.core.cssjs.globalJsConfig';

    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject An `exception` containing the caught error message.
     *
     * @argument "message" (`string`): The message to be displayed to the user. May contain HTML.
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
     * @argument  "videoId" (`string`): The ID of the video to be played.
     * @argument "providerName" (`string`): The name of the video provider (e.g. "vimeo" or "youtube").
     * @argument "dataUrl" (`ehough_curly_Url`): The embedded data URL.
     * @argument "embeddedImplementationName" (`string`): The name of the embedded implementation.
     *
     * @todo Determine what to do about the ehough_* classes being exposed in the docs
     *
     * @api
     * @since 3.1.0
     */
    const HTML_EMBEDDED = 'tubepress.core.html.embedded';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject A `string` containing the pagination HTML.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_PAGINATION = 'tubepress.core.html.pagination';

    /**
     * This event is fired when a TubePress builds the HTML for a TubePress "player".
     *
     * @subject A `string` containing the player HTML.
     *
     * @argument "video" (`tubepress_api_video_Video`): The video to be played.
     * @argument "playerName" (`string`): The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 3.1.0
     */
    const HTML_PLAYERLOCATION = 'tubepress.core.html.playerLocation';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject A `string` containing the HTML for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SEARCH_INPUT = 'tubepress.core.html.search.input';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject a `string` containing the HTML for the single video.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SINGLE_VIDEO = 'tubepress.core.html.singleVideo';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject A `string` containing the HTML for the thumbnail gallery.
     *
     * @argument "videoGalleryPage" (`tubepress_api_video_VideoGalleryPage`): The backing {@link tubepress_api_video_VideoGalleryPage}.
     * @argument "page" (`integer`): The page number.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_THUMBNAIL_GALLERY = 'tubepress.core.html.thumbnailGallery';

    /**
     * This event is fired immediately before TubePress prints out the HTML for its stylesheets. If you wish to
     * modify the stylesheet prior to it being output to the browser, see
     * {@link tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS}.
     *
     * @subject string The HTML for TubePress's stylesheets.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_STYLESHEETS_PRE = 'tubepress.core.html.stylesheets.pre';

    /**
     * This event is fired immediately after TubePress prints out the HTML for its stylesheets. 
     *
     * @subject string The HTML for TubePress's stylesheets.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_STYLESHEETS_POST = 'tubepress.core.html.stylesheets.post';

    /**
     * This event is fired immediately before TubePress prints out the HTML for its scripts. If you wish to
     * modify the javascript prior to it being output to the browser, see
     * {@link tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS}.
     *
     * @subject string The HTML for TubePress's scripts.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_SCRIPTS_PRE = 'tubepress.core.html.scripts.pre';

    /**
     * This event is fired immediately after TubePress prints out the HTML for its scripts.
     *
     * @subject string The HTML for TubePress's scripts.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_SCRIPTS_POST = 'tubepress.core.html.scripts.post';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject A `string` containing the HTTP body.
     *
     * @argument "request" (`ehough_shortstop_api_HttpRequest`):  The HTTP request.
     * @argument "response" (`ehough_shortstop_api_HttpResponse`): The HTTP response.
     *
     * @todo Determine what to do about the ehough_* classes being exposed in the docs
     *
     * @api
     * @since 3.1.0
     */
    const HTTP_RESPONSE = 'tubepress.core.http.response';

    /**
     * This event is fired when TubePress looks up the default value for an option. Typically
     * this only happens the first time TubePress is used on a system.
     *
     * @subject The default value of an option. May return one of a number of variable types.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DEFAULT_VALUE = 'tubepress.core.option.getDefaultValue';

    /**
     * This event is fired when TubePress looks up the label for an option.
     *
     * @subject A `string` containing the untranslated (i.e. in English) option label.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_LABEL = 'tubepress.core.option.getLabel';

    /**
     * This event is fired when TubePress looks up the description for an option.
     *
     * @subject A `string` containing the untranslated (i.e. in English) option description.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DESCRIPTION = 'tubepress.core.option.getDescription';

    /**
     * This event is fired when TubePress looks the acceptable values for an option. This
     * only applies to options that take on discrete values.
     *
     * @subject array The acceptable values for this option. This *may* be an associative array
     *                where the keys are values and the values are untranslated labels. You can use
     *                {@link tubepress_impl_util_LangUtils::isAssociativeArray()} to check the type of array.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DISCRETE_ACCEPTABLE_VALUES = 'tubepress.core.option.getDiscreteAcceptableValues';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @argument string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_SINGLE_PRE_VALIDATION_SET = 'tubepress.core.option.preValidationSet';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @argument string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT = 'tubepress.core.option.readFromExternalInput';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject mixed The incoming option value.
     *
     * @argument string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_ANY_PRE_VALIDATION_SET = 'tubepress.core.option.any.preValidationSet';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject mixed The incoming option value.
     *
     * @argument string 'optionName' The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_ANY_READ_FROM_EXTERNAL_INPUT = 'tubepress.core.option.any.readFromExternalInput';

    /**
     * This event is fired when TubePress loads a PHP/HTML template for a field on the options page.
     *
     * @subject ehough_contemplate_api_Template The template for the field.
     *
     * @api
     * @since 3.1.2
     */
    const OPTIONS_PAGE_FIELDTEMPLATE = 'tubepress.core.options.page.fieldTemplate';

    /**
     * This event is fired when TubePress loads a PHP/HTML template for the options page.
     *
     * @subject ehough_contemplate_api_Template The template for the page.
     *
     * @api
     * @since 3.1.2
     */
    const OPTIONS_PAGE_TEMPLATE = 'tubepress.core.options.page.finalTemplate';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for an embedded
     * video player.
     *
     * @subject ehough_contemplate_api_Template The embedded video player template.
     *
     * @argument string           'videoId'                    The ID of the video to be played.
     * @argument string           'providerName'               The name of the video provider (e.g. "vimeo" or "youtube").
     * @argument ehough_curly_Url 'dataUrl'                    The embedded data URL.
     * @argument string           'embeddedImplementationName' The name of the embedded implementation.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_EMBEDDED = 'tubepress.core.template.embedded';

    /**
     * This event is fired when TubePress builds the pagination HTML.
     *
     * @subject ehough_contemplate_api_Template The template for the pagination.
     *
     * @api
     * @since 3.2.0
     */
    const TEMPLATE_PAGINATION = 'tubepress.core.template.pagination';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for a TubePress
     * "player".
     *
     * @subject string The player HTML.
     *
     * @argument tubepress_api_video_Video 'video'      The video to be played.
     * @argument string                    'playerName' The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_PLAYERLOCATION = 'tubepress.core.template.player';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject ehough_contemplate_api_Template The template for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SEARCH_INPUT = 'tubepress.core.template.search.input';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @argument tubepress_api_video_Video 'video' The video to be played.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SINGLE_VIDEO = 'tubepress.core.template.singleVideo';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject ehough_contemplate_api_Template The template.
     *
     * @argument tubepress_api_video_VideoGalleryPage 'videoGalleryPage' The backing tubepress_api_video_VideoGalleryPage
     * @argument integer                              'page'             The page number.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_THUMBNAIL_GALLERY = 'tubepress.core.template.thumbnailGallery';

    /**
     * This event is fired when a TubePress builds a TubePress video. Some providers may add additional
     * arguments to this event.
     *
     * @subject tubepress_api_video_Video The TubePress video.
     *
     * @argument int   zeroBasedFeedIndex The zero-based index into the raw feed from which this video was built.
     * @argument mixed rawFeed            The "raw" unaltered feed from the provider.
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
