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

class_exists('TubePress')
|| require dirname(__FILE__) . '/../../../../TubePress.class.php';
TubePress::loadClasses(array('org_tubepress_api_filters_PluginManager',
    'org_tubepress_api_const_FilterPoint',
    'org_tubepress_impl_log_Log'));

class org_tubepress_impl_patterns_PluginManagerImpl implements org_tubepress_api_plugin_PluginManager
{
    const LOG_PREFIX = 'Plugin Manager';

    /**
     * Cached list of valid filter points. Used to validate filter registrations.
     */
    private $_validFilterPoints;

    /**
     * Internal two-dimensional array of all filters, first keyed by filter point name.
     */
    private $_filters;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /* set up an empty filters array */
        $this->_filters = array();

        /* initialize the valid filter points */
        $ref              = new ReflectionClass('org_tubepress_api_const_FilterPoint');
        $filterPointNames = $ref->getConstants();
        $filterPointFuncs = array();

        foreach ($filterPointNames as $filterPointName) {

            $this->_validFilterPoints[$filterPointName] = self::_getFilterMethodName($filterPointName);
            $this->_filters[$filterPointName] = array();
        }
    }

    /**
     * Run all filters for the given filter point.
     *  
     * @param string  $filterPoint The name of the filter point.
     * @param unknown $value       The value to send to the plugins.
     * 
     * @return unknown_type The modified value, or void.
     */
    public function filter($filterPoint, $value)
    {
        /* make sure this is a valid hook */
        if (!array_key_exists($filterPoint, $this->_validFilterPoints)) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Invalid filter point: "%s". Ignoring.', $filterPoint);
            return $value;
        }

        $filters = $this->_filters[$filterPoint];

        /* do we have anything to do? */
        if (empty($filters)) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'No filters registered for "%s".', $filterPoint);
            return $value;
        }

        $filterCount = count($filters);
        $filterIndex = 1;
        $args        = func_get_args();
    
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Now running %d filter(s) for "%s"', count($filters), $filterPoint);

        /* run all the callbacks for this filter name */
        foreach ($filters as $filter) {
    
            $callback       = array($filter, self::_getFilterMethodName($filterPoint));
            $filterAsString = get_class($filter);

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Now running filter %d of %d for filter point "%s": "%s"', 
                $filterIndex, $filterCount, $filterPoint, $filterAsString);
    
            try {

                $args[1] = $value;
                $value   = call_user_func_array($callback, array_slice($args, 1));
    
            } catch (Exception $e) {
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Caught exception running "%s" for filter point "%s": %s', $filterAsString, $filterPoint, $e->getMessage());
            }

            $filterIndex++;
        }
    
        /* return the modified value */
        return $value;
    }

    /**
     * Registers a filter.
     * 
     * @param string $filterPoint The name of the filter point.
     * @param object $plugin The  plugin instance.
     * 
     * @return void
     */
    function registerFilter($filterPoint, $plugin)
    {
        /* sanity check 1/2 */
        if (!array_key_exists($filterPoint, $this->_validFilterPoints)) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Invalid filter point: "%s". Ignoring filter registration for "%s".', $filterPoint, get_class($plugin));
            return;
        }

        $methodName = $this->_validFilterPoints[$filterPoint];

        /* sanity check 2/2 */
        if (!method_exists($plugin, $methodName) || !is_callable(array($plugin, $methodName))) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '"%s" must have a callable class method named "%s" to be registered for "%s" filter point. Ignoring this registration.',
                get_class($plugin), $methodName, $filterPoint);
            return;
        }

        /* looks good, let's register it */
        array_push($this->_filters[$filterPoint], $plugin);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Registered "%s" as a filter for "%s"', get_class($plugin), $filterPoint);
    }

    private static function _getFilterMethodName($filterPoint)
    {
        return 'alter_' . ucfirst($filterPointName);
    }
}