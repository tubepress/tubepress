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
 * Manages the registration and execution of plugins.
 */
interface org_tubepress_api_plugin_PluginManager
{
    const _ = 'org_tubepress_api_plugin_PluginManager';

    /**
     * Determines if there are any filters registered for the given point.
     *
     * @param string $filterPoint The filter point to check.
     *
     * @return boolean True if there are filters registered for the given point. False otherwise.
     */
    function hasFilters($filterPoint);

    /**
     * Run all filters for the given filter point.
     *
     * @param string  $filterPoint The name of the filter point.
     * @param unknown $value       The value to send to the plugins.
     *
     * @return unknown_type The modified value, or void.
     */
    function runFilters($filterPoint, $value);

    /**
     * Registers a filter.
     *
     * @param string $filterPoint The name of the filter point.
     * @param object $plugin      The plugin instance.
     *
     * @return void
     */
    function registerFilter($filterPoint, $plugin);

    /**
     * Determines if there are any listeners registered for the given event.
     *
     * @param string $eventName The event name to check.
     *
     * @return boolean True if there are listeners registered for the given event. False otherwise.
     */
    function hasListeners($eventName);

    /**
     * Run all listeners for the given event.
     *
     * @param string $eventName The name of the event.
     *
     * @return void
     */
     function notifyListeners($eventName);

    /**
     * Registers a listener.
     *
     * @param string $eventName The name of the event.
     * @param object $plugin    The plugin instance.
     *
     * @return void
     */
    function registerListener($eventName, $plugin);
}
