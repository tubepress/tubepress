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
 * Generates HTML.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_html_HtmlGeneratorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_html_HtmlGeneratorInterface';

    /**
     * @return string The HTML that should be displayed in the HTML <head> for CSS.
     *
     * @api
     * @since 4.0.0
     */
    function getCssHtml();

    /**
     * Generates the HTML for the given shortcode.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    function getHtmlForShortcode($shortCodeContent);

    /**
     * @return string The HTML that should be displayed for JS to be loaded onto the page. May occure in head
     *                or near footer.
     *
     * @api
     * @since 4.0.0
     */
    function getJsHtml();
}