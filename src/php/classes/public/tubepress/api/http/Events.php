<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
 * or as a constant reference (e.g. `tubepress_app_api_const_event_EventNames::CSS_JS_STYLESHEETS`). The latter
 * simply removes undocumented strings from your code and can help to prevent typos.
 *
 * @package TubePress\Const\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_http_Events
{
    /**
     * This event is fired when TubePress encounters an HTTP exception.
     *
     * @subject `tubepress_api_http_exception_RequestException` The HTTP request exception.
     *
     * @argument <var>response</var> (`tubepress_api_http_message_ResponseInterface`): Initially null, listeners
     *                               can populate with a response to properly handle the exception.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTTP_ERROR = 'tubepress.core.http.event.error';

    /**
     * This event is fired before TubePress executes a HTTP request.
     *
     * @subject `tubepress_api_http_message_RequestInterface` The HTTP request.
     *
     * @argument <var>response</var> (`tubepress_api_http_message_ResponseInterface`): Initially null, listeners can
     *              populate a response here to intercept the client request..
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTTP_REQUEST = 'tubepress.core.http.event.request';

    /**
     * This event is fired immediately after TubePress receives an HTTP response from the network, but before
     * any response body has been downloaded.
     *
     * @subject `tubepress_api_http_message_ResponseInterface` The HTTP response.
     *
     * @argument <var>request</var> (`tubepress_api_http_message_RequestInterface`):  The HTTP request.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTTP_RESPONSE_HEADERS = 'tubepress.core.http.event.response.headers';

    /**
     * This event is fired after TubePress fetches a HTTP response from the network.
     *
     * @subject `tubepress_api_http_message_ResponseInterface` The HTTP response.
     *
     * @argument <var>request</var> (`tubepress_api_http_message_RequestInterface`):  The HTTP request.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_HTTP_RESPONSE = 'tubepress.core.http.event.response';
}