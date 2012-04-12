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
    'org_tubepress_api_const_plugin_EventName',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_impl_log_Log',
));

class org_tubepress_impl_plugin_PluginManagerImpl implements org_tubepress_api_plugin_PluginManager
{
    /**
     * Log prefix.
     */
    private static $_logPrefix = 'Plugin Manager';

    /**
     * Cached list of valid filter points. Used to validate filter registrations.
     */
    private $_validFilterPoints;

    /**
     * Internal two-dimensional array of all filters, first keyed by filter point name.
     */
    private $_filters;

    /**
     * Cached list of valid event names. Used to validate listener registrations.
     */
    private $_validEventNames;

    /**
     * Internal two-dimensional array of all listeners, first keyed by event name.
     */
    private $_listeners;

    /**
     * Constructor.
     */
    public function __construct()
    {
        /** Set up an empty filters array. */
        $this->_filters = array();

        /** Set up an empty listeners array. */
        $this->_listeners = array();

        /** Initialize the valid filter points. */
        $ref              = new ReflectionClass('org_tubepress_api_const_plugin_FilterPoint');
        $filterPointNames = $ref->getConstants();
        $filterPointFuncs = array();

        /** Initialize the valid event names. */
        $ref            = new ReflectionClass('org_tubepress_api_const_plugin_EventName');
        $eventNames     = $ref->getConstants();
        $eventNameFuncs = array();

        foreach ($filterPointNames as $filterPointName) {

            $this->_validFilterPoints[$filterPointName] = self::_getFilterMethodName($filterPointName);
            $this->_filters[$filterPointName]           = array();
        }

        foreach ($eventNames as $eventName) {

            $this->_validEventNames[$eventName] = self::_getListenerMethodName($eventName);
            $this->_listeners[$eventName]       = array();
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
    public function runFilters($filterPoint, $value)
    {
        /** See if this filter point has any filters registered. */
        if (! $this->hasFilters($filterPoint)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'No filters registered for "%s".', $filterPoint);
            return $value;
        }

        $filters     = $this->_filters[$filterPoint];
        $filterCount = count($filters);
        $filterIndex = 1;
        $args        = func_get_args();

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Now running %d filter(s) for "%s"', $filterCount, $filterPoint);

        /* run all the callbacks for this filter name */
        foreach ($filters as $filter) {

            $callback       = array($filter, self::_getFilterMethodName($filterPoint));
            $filterAsString = get_class($filter);

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Running filter %d of %d for "%s" point: "%s"',
                $filterIndex, $filterCount, $filterPoint, $filterAsString);

            try {

                $args[1] = $value;
                $value   = call_user_func_array($callback, array_slice($args, 1));

            } catch (Exception $e) {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Caught exception running "%s" for filter point "%s": %s', $filterAsString, $filterPoint, $e->getMessage());
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
     * @param object $plugin      The plugin instance.
     *
     * @return void
     */
    public function registerFilter($filterPoint, $plugin)
    {
        /** Sanity check 1/3. */
        if (! is_object($plugin)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Only object instances can be registered as TubePress filter. Ingoring filter registration for "%s"', $filterPoint);
            return;
        }

        /** Sanity check 2/3. */
        if (! array_key_exists($filterPoint, $this->_validFilterPoints)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Invalid filter point: "%s". Ignoring filter registration for "%s".', $filterPoint, get_class($plugin));
            return;
        }

        $methodName = $this->_validFilterPoints[$filterPoint];

        /** Sanity check 3/3. */
        if (! method_exists($plugin, $methodName) || ! is_callable(array($plugin, $methodName))) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, '"%s" must have a callable class method named "%s" to be registered for "%s" filter point. Ignoring this registration.',
                get_class($plugin), $methodName, $filterPoint);
            return;
        }

        /** Looks good, let's register it. */
        array_push($this->_filters[$filterPoint], $plugin);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Registered "%s" as a filter for "%s"', get_class($plugin), $filterPoint);
    }

    /**
     * Determines if there are any filters registered for the given point.
     *
     * @param string $filterPoint The filter point to check.
     *
     * @return boolean True if there are filters registered for the given point. False otherwise.
     */
    public function hasFilters($filterPoint)
    {
        /** Make sure this is a valid hook. */
        if (! array_key_exists($filterPoint, $this->_validFilterPoints)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Invalid filter point: "%s". Ignoring.', $filterPoint);
            return false;
        }

        return ! empty($this->_filters[$filterPoint]);
    }

    /**
     * Determines if there are any listeners registered for the given event.
     *
     * @param string $eventName The event name to check.
     *
     * @return boolean True if there are listeners registered for the given event. False otherwise.
     */
    public function hasListeners($eventName)
    {
        /** Make sure this is a valid hook. */
        if (! array_key_exists($eventName, $this->_validEventNames)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Invalid event name: "%s". Ignoring.', $eventName);
            return false;
        }

        return ! empty($this->_listeners[$eventName]);
    }

    /**
     * Run all listeners for the given event.
     *
     * @param string $eventName The name of the event.
     *
     * @return void
     */
    public function notifyListeners($eventName)
    {
        if (! $this->hasListeners($eventName)) {

            return;
        }

        $listeners     = $this->_listeners[$eventName];
        $listenerCount = count($listeners);
        $listenerIndex = 1;
        $args          = func_num_args() > 1 ? array_slice(func_get_args(), 1) : null;

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Now running %d listeners(s) for "%s"', $listenerCount, $eventName);

        /* run all the callbacks for this event name */
        foreach ($listeners as $listener) {

            $callback         = array($listener, self::_getListenerMethodName($eventName));
            $listenerAsString = get_class($listener);

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Now running listener %d of %d for event "%s": "%s"',
                $listenerIndex, $listenerCount, $eventName, $listenerAsString);

            try {

                if ($args !== null) {
                    call_user_func_array($callback, $args);
                } else {
                    call_user_func($callback);
                }

            } catch (Exception $e) {
                org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Caught exception running "%s" for event "%s": %s', $listenerAsString, $eventName, $e->getMessage());
            }

            $listenerIndex++;
        }
    }

    /**
     * Registers a listener.
     *
     * @param string $eventName The name of the event.
     * @param object $plugin    The plugin instance.
     *
     * @return void
     */
    public function registerListener($eventName, $plugin)
    {
        /* sanity check 1/3 */
        if (!is_object($plugin)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Only object instances can be registered as TubePress listener. Ingoring listener registration for "%s"', $eventName);
            return;
        }

        /* sanity check 2/3 */
        if (!array_key_exists($eventName, $this->_validEventNames)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Invalid event name: "%s". Ignoring listener registration for "%s".', $eventName, get_class($plugin));
            return;
        }

        $methodName = $this->_validEventNames[$eventName];

        /* sanity check 3/3 */
        if (!method_exists($plugin, $methodName) || !is_callable(array($plugin, $methodName))) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, '"%s" must have a callable class method named "%s" to be registered for "%s" event. Ignoring this registration.',
                get_class($plugin), $methodName, $eventName);
            return;
        }

        /* looks good, let's register it */
        array_push($this->_listeners[$eventName], $plugin);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Registered "%s" as a listener for "%s"', get_class($plugin), $eventName);
    }

    private static function _getListenerMethodName($eventName)
    {
        return 'on_' . $eventName;
    }

    private static function _getFilterMethodName($filterPoint)
    {
        return 'alter_' . $filterPoint;
    }
}