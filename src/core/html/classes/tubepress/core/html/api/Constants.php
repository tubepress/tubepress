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
interface tubepress_core_html_api_Constants
{
    /**
     * This event is fired immediately before TubePress generates the HTML for its stylesheets. Listeners may add
     * additional HTML content to be printed immediately before the stylesheet HTML (i.e. <link> tags).
     *
     * @subject `string` The HTML to print before TubePress's stylesheets. This is initially empty.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_STYLESHEETS_PRE = 'tubepress.core.html.event.stylesheets.pre';

    /**
     * This event is fired immediately after TubePress assembles its list of stylesheets to print.
     *
     * @subject `tubepress_core_url_api_UrlInterface[]` An array of URLs for the stylesheets that TubePress will print in the HTML <head>
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_STYLESHEETS = 'tubepress.core.html.event.stylesheets';

    /**
     * This event is fired immediately after TubePress generates out the HTML for its stylesheets. Listeners may add
     * additional HTML content to be printed immediately after the stylesheet HTML (i.e. <link> tags).
     *
     * @subject `string` The HTML for TubePress's stylesheets.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_STYLESHEETS_POST = 'tubepress.core.html.event.stylesheets.post';

    /**
     * This event is fired immediately before TubePress generates the HTML for its scripts. Listeners may add
     * additional HTML content to be printed immediately before the script HTML (i.e. <script> tags).
     *
     * @subject `string` The HTML to print before TubePress's scripts. This is initially empty.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SCRIPTS_PRE = 'tubepress.core.html.event.scripts.pre';

    /**
     * This event is fired immediately after TubePress assembles its list of scripts to print.
     *
     * @subject `tubepress_core_url_api_UrlInterface[]` An array of URLs for the scripts that TubePress will print in the HTML <head>
     *                                                  or near the closing </body> tag.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SCRIPTS = 'tubepress.core.html.event.scripts';

    /**
     * This event is fired immediately after TubePress generates out the HTML for its scripts. Listeners may add
     * additional HTML content to be printed immediately after the script HTML (i.e. <script> tags).
     *
     * @subject `string` The HTML for TubePress's scripts.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_SCRIPTS_POST = 'tubepress.core.html.event.scripts.post';

    /**
     * This event is fired when TubePress builds the global `TubePressJsConfig` JavaScript object.
     *
     * @subject `array` An associative `array` that will be converted into JSON and applied as global JS configuration
     *                  for TubePress.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_GLOBAL_JS_CONFIG = 'tubepress.core.html.event.globalJsConfig';

    /**
     * This event is fired when TubePress generates its "primary" HTML (e.g. a media gallery,
     * search input widget, embedded media player, etc).
     *
     * @subject `string` Initially null, listeners are expected to populate the subject with HTML.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_PRIMARY_HTML = 'tubepress.core.html.event.primaryHtml';

    /**
     * This event is fired when TubePress encounters an error during processing and is
     * about to return an error message to the screen.
     *
     * @subject `Exception` A PHP exception containing the caught error message.
     *
     * @argument <var>htmlForUser</var> (`string`): Initially the message from the exception,
     *                                              the message to be displayed to the user. May contain HTML.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_EXCEPTION_CAUGHT = 'tubepress.core.html.event.exception';


    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_OUTPUT = 'output';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_HTTPS = 'https';

    /**
     * @api
     * @since 4.0.0
     */
    const OPTION_GALLERY_ID = 'galleryId';
}