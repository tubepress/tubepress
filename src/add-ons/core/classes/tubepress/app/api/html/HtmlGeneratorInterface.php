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
 * Generates HTML.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_html_HtmlGeneratorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_html_HtmlGeneratorInterface';

    /**
     * Generates the primary HTML.
     *
     * @return string The HTML, or the error message if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    function getHtml();

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    function getUrlsCSS();

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    function getUrlsJS();

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getCSS();

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getJS();
}