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
 * Manages the registration and execution of filters.
 */
interface org_tubepress_api_patterns_FilterManager
{
    /**
     * Run all filters for the specified execution point, giving each filter a chance
     *  to modify the given value.
     *  
     * @param string       $name  The execution point name.
     * @param unknown_type $value The value to send to the filters.
     * 
     * @return unknown_type The modified value.
     */
    function runFilters($name, $value);

    /**
     * Registers a filter for the specified execution point.
     * 
     * @param string   $name     The execution point name.
     * @param callback $callback The filter callback.
     * 
     * @return void
     */
    function registerFilter($name, $callback);
}
