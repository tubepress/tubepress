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
 * A detailed list of the core TubePress events.
 *
 * Each event name can be referred to either by its raw name (e.g. `tubepress.core.cssjs.stylesheets`)
 * or as a constant reference (e.g. `tubepress_core_api_const_event_EventNames::CSS_JS_STYLESHEETS`). The latter
 * simply removes undocumented strings from your code and can help to prevent typos.
 *
 * @package TubePress\Const\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_const_event_EventNames
{
    /**
     * This event is fired when TubePress is about to print its stylesheets to the HTML head.
     *
     * @subject `tubepress_core_api_url_UrlInterface[]` An array of URLs for the stylesheets that TubePress will print in the HTML <head>
     *
     * @api
     * @since 4.0.0
     */
    const CSS_JS_STYLESHEETS = 'tubepress.core.cssjs.stylesheets';

    /**
     * This event is fired when TubePress is about to print its JS files (either in `<head>` or near `</body>`).
     *
     * @subject `tubepress_core_api_url_UrlInterface[]` An `array` of URLs for the scripts that TubePress will print in the HTML.
     *
     * @api
     * @since 4.0.0
     */
    const CSS_JS_SCRIPTS = 'tubepress.core.cssjs.scripts';

    /**
     * This event is fired when TubePress builds the gallery initialization JSON, which is inserted immediately
     * after each gallery as it appears in the HTML.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as
     *                  init code for the gallery in JavaScript.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GALLERY_INIT = 'tubepress.core.cssjs.galleryInit';

    /**
     * This event is fired when TubePress builds the `TubePressJsConfig` object.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as global JS configuration
     *                  for TubePress.
     *
     * @api
     * @since 3.1.0
     */
    const CSS_JS_GLOBAL_JS_CONFIG = 'tubepress.core.cssjs.globalJsConfig';

    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject `Exception` A PHP exception containing the caught error message.
     *
     * @argument <var>message</var> (`string`): The message to be displayed to the user. May contain HTML.
     *
     * @api
     * @since 3.1.0
     */
    const ERROR_EXCEPTION_CAUGHT = 'tubepress.core.error.exceptionCaught';

    /**
     * This event is fired when TubePress builds the HTML for an embedded video player.
     *
     * @subject `string` The HTML for the embedded video player.
     *
     * @argument <var>videoId</var> (`string`): The ID of the video to be played.
     * @argument <var>providerName</var> (`string`): The name of the video provider (e.g. "vimeo" or "youtube").
     * @argument <var>dataUrl</var> (`tubepress_core_api_url_UrlInterface`): The embedded data URL.
     * @argument <var>embeddedImplementationName</var> (`string`): The name of the embedded implementation.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_EMBEDDED = 'tubepress.core.html.embedded';

    /**
     * This event is fired when TubePress generates HTML for a shortcode.
     *
     * @subject `string`|`null` Listeners are expected to populate the subject with HTML
     *
     * @api
     * @since 4.0.0
     */
    const HTML_GENERATION = 'tubepress.core.html.generation';

    /**
     * This event is fired when a TubePress builds the HTML for pagination.
     *
     * @subject `string` The pagination HTML.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_PAGINATION = 'tubepress.core.html.pagination';

    /**
     * This event is fired when a TubePress builds the HTML for a TubePress "player".
     *
     * @subject `string` The player HTML.
     *
     * @argument <var>video</var> (`{@link tubepress_core_api_video_Video}`): The video to be played.
     * @argument <var>playerName</var> (`string`): The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 3.1.0
     */
    const HTML_PLAYERLOCATION = 'tubepress.core.html.playerLocation';

    /**
     * This event is fired when TubePress builds HTML for a standard (non-Ajax) search input form.
     *
     * @subject `string` The HTML for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SEARCH_INPUT = 'tubepress.core.html.search.input';

    /**
     * This event is fired when TubePress builds HTML for a single video (not inside a gallery).
     *
     * @subject `string` The HTML for the single video.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_SINGLE_VIDEO = 'tubepress.core.html.singleVideo';

    /**
     * This event is fired when TubePress builds the HTML for a thumbnail gallery.
     *
     * @subject `string` The HTML for the thumbnail gallery.
     *
     * @argument <var>videoGalleryPage</var> (`{@link tubepress_core_api_video_VideoGalleryPage}`): The backing {@link tubepress_core_api_video_VideoGalleryPage}.
     * @argument <var>page</var> (`integer`): The page number.
     *
     * @api
     * @since 3.1.0
     */
    const HTML_THUMBNAIL_GALLERY = 'tubepress.core.html.thumbnailGallery';

    /**
     * This event is fired immediately before TubePress prints out the HTML for its stylesheets. If you wish to
     * modify the stylesheet URL prior to it being output to the browser, see
     * {@link tubepress_core_api_const_event_EventNames::CSS_JS_STYLESHEETS}.
     *
     * @subject `string` The HTML for TubePress's stylesheets.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_STYLESHEETS_PRE = 'tubepress.core.html.stylesheets.pre';

    /**
     * This event is fired immediately after TubePress prints out the HTML for its stylesheets. 
     *
     * @subject `string` The HTML for TubePress's stylesheets.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_STYLESHEETS_POST = 'tubepress.core.html.stylesheets.post';

    /**
     * This event is fired immediately before TubePress prints out the HTML for its scripts. If you wish to
     * modify the javascript prior to it being output to the browser, see
     * {@link tubepress_core_api_const_event_EventNames::CSS_JS_SCRIPTS}.
     *
     * @subject `string` The HTML for TubePress's scripts.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_SCRIPTS_PRE = 'tubepress.core.html.scripts.pre';

    /**
     * This event is fired immediately after TubePress prints out the HTML for its scripts.
     *
     * @subject `string` The HTML for TubePress's scripts.
     *
     * @api
     * @since 3.1.3
     */
    const HTML_SCRIPTS_POST = 'tubepress.core.html.scripts.post';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject `tubepress_core_api_http_RequestException` The HTTP request exception.
     *
     * @argument <var>request</var> (`tubepress_core_api_http_RequestInterface`):  The HTTP request.
     * @argument <var>response</var> (`tubepress_core_api_http_ResponseInterface`): The HTTP response. May be null.
     *
     * @api
     * @since 4.0.0
     */
    const HTTP_ERROR = 'tubepress.core.http.error';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject `tubepress_core_api_http_RequestInterface` The HTTP request.
     *
     * @argument <var>response</var> (`tubepress_core_api_http_ResponseInterface`): Initially null, listeners can
     *              populate a response here to intercept the client request..
     *
     * @api
     * @since 4.0.0
     */
    const HTTP_REQUEST = 'tubepress.core.http.request';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject `tubepress_core_api_http_ResponseInterface` The HTTP response.
     *
     * @argument <var>request</var> (`tubepress_core_api_http_RequestInterface`):  The HTTP request.
     *
     * @api
     * @since 4.0.0
     */
    const HTTP_RESPONSE_HEADERS = 'tubepress.core.http.response.headers';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject `tubepress_core_api_http_ResponseInterface` The HTTP response.
     *
     * @argument <var>request</var> (`tubepress_core_api_http_RequestInterface`):  The HTTP request.
     *
     * @api
     * @since 4.0.0
     */
    const HTTP_RESPONSE = 'tubepress.core.http.response';

    /**
     * This event is fired when TubePress looks up the default value for an option. Typically
     * this only happens the first time TubePress is used on a system, but may also fire
     * after an upgrade.
     *
     * @subject `mixed` The default value of an option. May be null.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DEFAULT_VALUE = 'tubepress.core.option.getDefaultValue';

    /**
     * This event is fired when TubePress looks up the label for an option.
     *
     * @subject `string` The untranslated (i.e. in English) option label.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_LABEL = 'tubepress.core.option.getLabel';

    /**
     * This event is fired when TubePress looks up the description for an option.
     *
     * @subject `string` The untranslated (i.e. in English) option description.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DESCRIPTION = 'tubepress.core.option.getDescription';

    /**
     * This event is fired when TubePress looks the acceptable values for an option. This
     * only applies to options that take on discrete values.
     *
     * @subject `array` The acceptable values for this option. This *may* be an associative array
     *                  where the keys are values and the values are untranslated labels. You can use
     *                  {@link tubepress_impl_util_LangUtils::isAssociativeArray()} to check the type of array.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_GET_DISCRETE_ACCEPTABLE_VALUES = 'tubepress.core.option.getDiscreteAcceptableValues';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject `mixed` The incoming option value.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_SINGLE_PRE_VALIDATION_SET = 'tubepress.core.option.preValidationSet';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject `mixed` The incoming option value.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT = 'tubepress.core.option.readFromExternalInput';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being set. It is fired
     * *before* any validation takes place, so use caution when handling these values.
     *
     * @subject `mixed` The incoming option value.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_ANY_PRE_VALIDATION_SET = 'tubepress.core.option.any.preValidationSet';

    /**
     * This event is fired when a TubePress option (a name-value pair) is being read from external input.
     *
     * @subject `mixed` The incoming option value.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     *
     * @api
     * @since 3.2.0
     */
    const OPTION_ANY_READ_FROM_EXTERNAL_INPUT = 'tubepress.core.option.any.readFromExternalInput';

    /**
     * This event is fired when TubePress loads a PHP/HTML template for a field on the options page.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template for the field.
     *
     * @api
     * @since 3.1.2
     */
    const OPTIONS_PAGE_FIELDTEMPLATE = 'tubepress.core.options.page.fieldTemplate';

    /**
     * This event is fired when TubePress loads a PHP/HTML template for the options page.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template for the page.
     *
     * @api
     * @since 3.1.2
     */
    const OPTIONS_PAGE_TEMPLATE = 'tubepress.core.options.page.finalTemplate';

    /**
     * This event is fired when TubePress chooses the tubepress_core_api_player_PlayerLocationInterface
     * to generate player HTML.
     *
     * @subject `tubepress_core_api_video_Video` The video being played.
     *
     * @argument <var>requestedPlayerLocation</var> (`string`) The current value of `playerLocation`.
     * @argument <var>provider</var> (`tubepress_core_api_player_PlayerLocationInterface`|`null`) The selected tubepress_core_api_player_PlayerLocationInterface. Initially null,
     *                               listeners are expected to populate this argument.
     *
     * @api
     * @since 4.0.0
     */
    const SELECT_PLAYER_LOCATION = 'tubepress.core.select.playerLocation';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for an embedded
     * video player.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The embedded video player template.
     *
     * @argument <var>videoId</var> (`string`): The ID of the video to be played.
     * @argument <var>providerName</var> (`string`): The name of the video provider (e.g. "vimeo" or "youtube").
     * @argument <var>dataUrl</var> (`tubepress_core_api_url_UrlInterface`): The embedded data URL.
     * @argument <var>embeddedImplementationName</var> (`string`): The name of the embedded implementation.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_EMBEDDED = 'tubepress.core.template.embedded';

    /**
     * This event is fired when TubePress builds the pagination HTML.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template for the pagination.
     *
     * @api
     * @since 3.2.0
     */
    const TEMPLATE_PAGINATION = 'tubepress.core.template.pagination';

    /**
     * This event is fired when a TubePress builds the PHP/HTML template for a TubePress
     * "player".
     *
     * @subject `string` The player HTML.
     *
     * @argument <var>video</var> (`{@link tubepress_core_api_video_Video}`): The video to be played.
     * @argument <var>playerName</var> (`string`): The name of the TubePress player (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_PLAYERLOCATION = 'tubepress.core.template.player';

    /**
     * This event is fired when TubePress builds the template for a standard (non-Ajax) search input form.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template for the search input.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SEARCH_INPUT = 'tubepress.core.template.search.input';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a single video (not inside a gallery)
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template.
     *
     * @argument <var>video</var> (`{@link tubepress_core_api_video_Video}`): The video to be played.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_SINGLE_VIDEO = 'tubepress.core.template.singleVideo';

    /**
     * This event is fired when TubePress builds the PHP/HTML template for a thumbnail gallery.
     *
     * @subject `tubepress_core_api_template_TemplateInterface` The template.
     *
     * @argument <var>videoGalleryPage</var> (`{@link tubepress_core_api_video_VideoGalleryPage}`): The backing {@link tubepress_core_api_video_VideoGalleryPage}
     * @argument <var>page</var> (`integer`): The page number.
     *
     * @api
     * @since 3.1.0
     */
    const TEMPLATE_THUMBNAIL_GALLERY = 'tubepress.core.template.thumbnailGallery';

    /**
     * This event is fired when a TubePress builds a TubePress video. Some providers may add additional
     * arguments to this event.
     *
     * @subject {@link tubepress_core_api_video_Video} The TubePress video.
     *
     * @argument <var>zeroBasedFeedIndex</var> (`int`): The zero-based index into the raw feed from which this video was built.
     * @argument <var>rawFeed</var> (`mixed`): The "raw" unaltered feed from the provider.
     *
     * @api
     * @since 3.1.0
     */
    const VIDEO_CONSTRUCTION = 'tubepress.core.videoConstruction';

    /**
     * This event is fired when a TubePress builds a tubepress_core_api_video_VideoGalleryPage.
     *
     * @subject {@tubepress_core_api_video_VideoGalleryPage} The video gallery page being built.
     *
     * @api
     * @since 3.1.0
     */
    const VIDEO_GALLERY_PAGE = 'tubepress.core.videoGalleryPageConstruction';
}
