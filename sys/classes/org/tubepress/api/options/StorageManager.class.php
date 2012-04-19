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
 * Handles persistent storage of TubePress options
 *
 */
interface org_tubepress_api_options_StorageManager
{
    const _ = 'org_tubepress_api_options_StorageManager';

    /**
     * Determines if an option exists
     *
     * @param string $optionName The name of the option in question
     *
     * @return boolean True if the option exists, false otherwise
     */
    function exists($optionName);

    /**
     * Initializes the storage
     *
     * @return void
     */
    function init();

    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return unknown_type The option's value
     */
    function get($optionName);

    /**
     * Sets an option value
     *
     * @param string       $optionName  The option name
     * @param unknown_type $optionValue The option value
     *
     * @return True on success, otherwise a string error message.
     */
    function set($optionName, $optionValue);
}
