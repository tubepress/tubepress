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
 * Implementation of tubepress_spi_options_StorageManager that uses the
 * regular WordPress options API
 */
class tubepress_plugins_wordpress_impl_options_WordPressStorageManager extends tubepress_impl_options_AbstractStorageManager
{
    /*
     * Prefix all our option names in the WordPress DB
     * with this value. Helps avoid naming conflicts.
     */
    private static $_optionPrefix = "tubepress-";

    /**
     * Creates an option in storage
     *
     * @param mixed $optionName  The name of the option to create
     * @param mixed $optionValue The default value of the new option.
     *
     * @return void
     */
    protected function create($optionName, $optionValue)
    {
        $wordPressFunctionWrapperService = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();
        
        $wordPressFunctionWrapperService->add_option(self::$_optionPrefix . $optionName, $optionValue);
    }

    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return mixed The option's value
     */
    public function get($optionName)
    {
        $wordPressFunctionWrapperService = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();
        
        return $wordPressFunctionWrapperService->get_option(self::$_optionPrefix . $optionName);
    }

    /**
     * Sets an option to a new value, without validation
     *
     * @param string $optionName  The name of the option to update
     * @param mixed  $optionValue The new option value
     *
     * @return void
     */
    protected function setOption($optionName, $optionValue)
    {
        $wordPressFunctionWrapperService = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();
        
        $wordPressFunctionWrapperService->update_option(self::$_optionPrefix . $optionName, $optionValue);
    }

    /**
     * @return array All the option names currently in this storage manager.
     */
    protected function getAllOptionNames()
    {
        global $wpdb;

        $raw = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'tubepress-%'");

        if (! $raw || ! is_array($raw)) {

            return array();
        }

        $toReturn = array();

        foreach ($raw as $optionName) {

            $toReturn[] = str_replace(self::$_optionPrefix, '', $optionName->option_name);
        }

        return $toReturn;
    }
}
