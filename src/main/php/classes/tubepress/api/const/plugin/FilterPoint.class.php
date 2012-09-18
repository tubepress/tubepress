<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
 * Official filter points around the TubePress core.
 */
interface org_tubepress_api_const_plugin_FilterPoint
{
    /**
     * function alter_paginationHtml($paginationHtml, $providerName);
     */
    const HTML_PAGINATION = 'paginationHtml';

    /**
     * Modify the name-value pairs sent to TubePressGallery.init().
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('galleryInitJavaScript', $yourClassInstance);
     *
     *
     * @param array $args An associative array (name => value) of args to send to TubePressGallery.init();
     *
     * @return array The (possibly modified) array. Never null.
     *
     *
     * function alter_galleryInitJavaScript($args);
     */
    const JAVASCRIPT_GALLERYINIT = 'galleryInitJavaScript';




}

