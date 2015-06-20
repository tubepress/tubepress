<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_event_Events
{
    /**
     * This event is fired after TubePress builds the gallery initialization JSON, which is inserted immediately
     * after each gallery as it appears in the HTML.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as
     *                  init code for the gallery in JavaScript.
     *
     * @argument <var>mediaPage</var> (`{@link tubepress_app_api_media_MediaPage}`): The backing {@link tubepress_app_api_media_MediaPage}.
     * @argument <var>pageNumber</var> (`integer`): The page number.
     *
     * @api
     * @since 4.0.0
     */
    const GALLERY_INIT_JS = 'tubepress.app.gallery.initJs';

    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject `Exception` A PHP exception containing the caught error message.
     *
     * @api
     * @since 4.0.0
     */
    const HTML_EXCEPTION_CAUGHT = 'tubepress.app.html.exception.caught';

    /**
     * This event is fired when TubePress builds the global `TubePressJsConfig` JavaScript object.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as global JS configuration
     *                  for TubePress.
     *
     * @api
     * @since 4.0.0
     */
    const HTML_GLOBAL_JS_CONFIG = 'tubepress.app.html.globalJsConfig';

    /**
     * This event is fired when TubePress generates its "primary" HTML (e.g. a media gallery,
     * search input widget, embedded media player, etc).
     *
     * @subject `string` Initially null, listeners are expected to populate the subject with HTML.
     *
     * @api
     * @since 4.0.0
     */
    const HTML_GENERATION = 'tubepress.app.html.generation';

    /**
     * This event is fired after TubePress generates its "primary" HTML (e.g. a media gallery,
     * search input widget, embedded media player, etc).
     *
     * @subject `string` The HTML that will be returned to the user.
     *
     * @api
     * @since 4.1.10
     */
    const HTML_GENERATION_POST = 'tubepress.app.html.generation.post';

    /**
     * This event is fired immediately after TubePress assembles its list of scripts to print.
     *
     * @subject `tubepress_platform_api_url_UrlInterface[]` An array of URLs for the scripts that TubePress will print in the HTML <head>
     *                                                      or near the closing </body> tag.
     *
     * @api
     * @since 4.0.0
     */
    const HTML_SCRIPTS = 'tubepress.app.html.scripts';

    /**
     * This event is fired immediately after TubePress assembles its list of stylesheets to print.
     *
     * @subject `tubepress_platform_api_url_UrlInterface[]` An array of URLs for the stylesheets that TubePress will print in the HTML <head>
     *
     * @api
     * @since 4.0.0
     */
    const HTML_STYLESHEETS = 'tubepress.app.html.stylesheets';

    /**
     * This event is fired immediately after TubePress assembles its list of scripts to print in the options GUI.
     *
     * @subject `tubepress_platform_api_url_UrlInterface[]` An array of URLs for the scripts that TubePress will print in the HTML <head>
     *                                                      or near the closing </body> tag.
     *
     * @api
     * @since 4.0.0
     */
    const HTML_SCRIPTS_ADMIN = 'tubepress.app.html.scripts.admin';

    /**
     * This event is fired immediately after TubePress assembles its list of stylesheets to print in the options GUI.
     *
     * @subject `tubepress_platform_api_url_UrlInterface[]` An array of URLs for the stylesheets that TubePress will print in the HTML <head>
     *
     * @api
     * @since 4.0.0
     */
    const HTML_STYLESHEETS_ADMIN = 'tubepress.app.html.stylesheets.admin';

    /**
     * @api
     * @since 4.0.0
     */
    const HTTP_AJAX = 'tubepress.app.http.ajax';

    /**
     * Fired when an HTTP-based media provider constructs a new item.
     *
     * @subject `tubepress_app_api_media_MediaItem` The media item being constructed.
     *
     * @arguments Varies based on the provider.
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_HTTP_NEW = 'tubepress.app.media.item.http.new';

    /**
     * This event is fired when TubePress constructs the URL for a single media item.
     *
     * @subject {@tubepress_platform_api_url_UrlInterface} The URL for the media item.
     *
     * @argument <var>itemId</var> (`string`) The item ID.
     */
    const MEDIA_ITEM_HTTP_URL = 'tubepress.app.media.item.http.url';

    /**
     * This event is fired when a TubePress collects a new media item.
     *
     * @subject {@link tubepress_app_api_media_MediaItem} The media item.
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_NEW = 'tubepress.app.media.item.new';

    /**
     * This event is fired when TubePress receives a request to fetch a media item
     * from a provider.
     *
     * @subject `string` The media item ID.
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_ITEM_REQUEST = 'tubepress.app.media.item.request';

    /**
     * This event is fired when TubePress constructs the URL for a media page.
     *
     * @subject {@tubepress_platform_api_url_UrlInterface} The URL for the media page.
     *
     * @argument <var>pageNumber</var> (`int`) The page number.
     */
    const MEDIA_PAGE_HTTP_NEW = 'tubepress.app.media.page.http.new';

    /**
     * This event is fired when TubePress constructs the URL for a media page.
     *
     * @subject {@tubepress_platform_api_url_UrlInterface} The URL for the media page.
     *
     * @argument <var>pageNumber</var> (`int`) The page number.
     */
    const MEDIA_PAGE_HTTP_URL = 'tubepress.app.media.page.http.url';

    /**
     * This event is fired when a TubePress collects a new tubepress_app_api_media_MediaPage.
     *
     * @subject {@tubepress_app_api_media_MediaPage} The page being built.
     *
     * @argument <var>pageNumber</var> (`int`) The page number of this page.
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_PAGE_NEW = 'tubepress.app.media.page.new';

    /**
     * This event is fired when TubePress receives a request to fetch a new page of media
     * items from a provider.
     *
     * @subject `string` The current value of the "mode" option.
     *
     * @argument <var>pageNumber</var> (`int`) The page number.
     *
     * @api
     * @since 4.0.0
     */
    const MEDIA_PAGE_REQUEST = 'tubepress.app.media.page.request';

    /**
     * This event is fired when a name-value pair is being read from external input.
     *
     * @subject `mixed` The incoming value.
     *
     * @argument <var>optionName</var> (`string`): The incoming name.
     *
     * @api
     * @since 4.0.0
     */
    const NVP_FROM_EXTERNAL_INPUT = 'tubepress.app.nvp.fromExternalInput';

    /**
     * This event is fired when TubePress looks the acceptable values for an option. This
     * only applies to options that take on discrete values.
     *
     * @subject `array`|`null` Initially null, the acceptable values for this option. This *may* be an associative array
     *                         where the keys are values and the values are untranslated labels. You can use
     *                         {@link tubepress_platform_impl_util_LangUtils::isAssociativeArray()} to check the type of array.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const OPTION_ACCEPTABLE_VALUES = 'tubepress.app.option.acceptableValues';

    /**
     * This event is fired when TubePress looks up the default value for a specific option. Typically
     * this only happens the first time TubePress is used on a system, but may also fire
     * after an upgrade.
     *
     * @subject `mixed` The default value of an option. May be null.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const OPTION_DEFAULT_VALUE = 'tubepress.app.option.defaultValue';

    /**
     * This event is fired when TubePress looks up the description for a specific option.
     *
     * @subject `string` The untranslated (i.e. in English) option description.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const OPTION_DESCRIPTION = 'tubepress.app.option.description';

    /**
     * This event is fired when TubePress looks up the label for a specific option.
     *
     * @subject `string` The untranslated (i.e. in English) option label.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const OPTION_LABEL = 'tubepress.app.option.label';

    /**
     * This event is fired when any TubePress option (a name-value pair) is being set.
     *
     * @subject `string[]` The errors found for this option's value. Initially empty, listeners may add
     *                     to the array.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     * @argument <var>optionValue</var> (`mixed`): The value of the option being set.
     *
     * @api
     * @since 4.0.0
     */
    const OPTION_SET = 'tubepress.app.option.set';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_SELECT      = 'tubepress.app.template.select';

    /**
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_PRE_RENDER  = 'tubepress.app.template.pre';

    /**
     * This event is fired after TubePress renders a template.
     *
     * @subject `string` The result of the template render.
     *
     * @api
     * @since 4.0.0
     */
    const TEMPLATE_POST_RENDER = 'tubepress.app.template.post';
}