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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_api_plugin_PluginManager',
));

/**
 * Class for managing HTTP Transports and making HTTP requests.
 */
class org_tubepress_impl_http_DefaultHttpRequestParameterService implements org_tubepress_api_http_HttpRequestParameterService
{
    private static $_logPrefix = 'Default HTTP Request Param Service';

    /**
     * A handle to the plugin manager.
     */
    private $_pluginManager;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $ioc                  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
    }

    /**
     * Gets the parameter value from PHP's $_REQUEST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return unknown_type The raw value of the parameter. Can be anything that would
     *                       otherwise be found in PHP's $_REQUEST array. Returns null
     *                       if the parameter is not set on this request.
     */
    function getParamValue($name)
    {
        /** Are we sure we have it? */
        if (!($this->hasParam($name))) {

            return null;
        }

        $rawValue = $_REQUEST[$name];

        /** Run it through the filters, if any. */
        return $this->_pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::VARIABLE_READ_FROM_EXTERNAL_INPUT, $rawValue, $name);
    }

    /**
     * Gets the parameter value from PHP's $_REQUEST array. If the hasParam($name) returs false, this
     *  behaves just like getParamvalue($name). Otherwise, if the raw parameter value is numeric, a conversion
     *  will be attempted.
     *
     * @param string $name    The name of the parameter.
     * @param int    $default The default value is the raw value is not integral.
     *
     * @return unknown_type The raw value of the parameter. Can be anything that would
     *                       otherwise be found in PHP's $_REQUEST array. Returns null
     *                       if the parameter is not set on this request.
     */
    function getParamValueAsInt($name, $default)
    {
        $raw = $this->getParamValue($name);

        /** Not numeric? */
        if (! is_numeric($raw) || ($raw < 1)) {

            return $default;
        }

        return (int) $raw;
    }

    /**
     * Determines if the parameter is set in PHP's $_REQUEST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return unknown_type True if the parameter is found in PHP's $_REQUEST array, false otherwise.
     */
    function hasParam($name)
    {
        return array_key_exists($name, $_REQUEST);
    }
}

