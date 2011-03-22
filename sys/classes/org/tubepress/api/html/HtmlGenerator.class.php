<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Handles HTML.
 */
interface org_tubepress_api_html_HtmlGenerator
{
    function getHeadJqueryIncludeString();

    function getHeadInlineJavaScriptString();

    function getHeadTubePressJsIncludeString();

    function getHeadTubePressCssIncludeString();

    function getHeadMetaString();

    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param string $shortCodeContent The shortcode content. May be empty or null.
     *
     * @return The HTML for the given shortcode, or the error message if there was a problem.
     */
    function getHtmlForShortcode($shortCodeContent);
}

