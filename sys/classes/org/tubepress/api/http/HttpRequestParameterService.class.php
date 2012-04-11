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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_http_HttpMessage',
));

/**
 * Pulls out info from $_REQUEST.
 */
interface org_tubepress_api_http_HttpRequestParameterService
{
    const _ = 'org_tubepress_api_http_HttpRequestParameterService';

    /**
     * Gets the parameter value from PHP's $_REQUEST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return unknown_type The raw value of the parameter. Can be anything that would
     *                       otherwise be found in PHP's $_REQUEST array. Returns null
     *                       if the parameter is not set on this request.
     */
    function getParamValue($name);

    /**
     * Determines if the parameter is set in PHP's $_REQUEST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return unknown_type True if the parameter is found in PHP's $_REQUEST array, false otherwise.
     */
    function hasParam($name);
}
