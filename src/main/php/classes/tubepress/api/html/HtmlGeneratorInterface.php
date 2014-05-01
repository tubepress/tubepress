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
 * Generates HTML for use in the <head>.
 */
interface tubepress_api_html_HtmlGeneratorInterface
{
    const _ = 'tubepress_api_html_HtmlGeneratorInterface';

    /**
     * @return string The HTML that should be displayed in the HTML <head>.
     */
    function getCssHtml();

    /**
     * Generates the HTML for the given shortcode.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     */
    function getHtmlForShortcode($shortCodeContent);

    /**
     * @return string The HTML that should be displayed in the HTML footer (just before </html>)
     */
    function getJsHtml();
}