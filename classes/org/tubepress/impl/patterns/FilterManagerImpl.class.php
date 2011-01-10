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

function_exists('tubepress_load_classes')
|| require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_patterns_FilterManager',
    'org_tubepress_api_const_FilterExecutionPoint',
    'org_tubepress_impl_log_Log'));

class org_tubepress_impl_patterns_FilterManagerImpl implements org_tubepress_api_patterns_FilterManager
{
    const LOG_PREFIX = 'Filter Manager';
    
    private $_filters;

    public function __construct()
    {
        $this->_filters = array();
    }

    public function runFilters($name, $value)
    {
        /* no callbacks registered for this filter? */
        if (!isset($this->_filters[$name])) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, "No filters registered for $name");
            return $value;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, "Now running %d filter(s) for \"%s\" execution point", sizeof($this->_filters[$name]), $name);
        
        $args = func_get_args();
        
        /* run all the callbacks for this filter name */
        foreach ($this->_filters[$name] as $callback) {

            try {

                $args[1] = $value;
                $value = call_user_func_array($callback, array_slice($args, 1));

            } catch (Exception $e) {
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Caught exception running filter: %s', $e->getMessage());
            }

        }

        /* return the modified value */
        return $value;
    }

    public function registerFilter($name, $callback) {

        /* sanity check on the callback */
        if (!is_callable($callback)) {
            throw new Exception("$callback is not a valid filter callback");
        }

        /* sanity check on the filter name */
        if (!is_string($name)) {
            throw new Exception('Only strings can be used to identify a filter');
        }

        /* first time we're seeing this filter name? */
        if (!isset($this->_filters[$name])) {
            $this->_filters[$name] = array();
        }

        /* everything looks good. */
        array_push($this->_filters[$name], $callback);
    }
}

?>
