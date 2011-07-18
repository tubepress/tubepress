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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_options_AbstractStorageManager'
));

/**
 * Implementation of org_tubepress_api_options_StorageManager that uses the
 * regular WordPress options API
 */
class org_tubepress_impl_options_WordPressStorageManager extends org_tubepress_impl_options_AbstractStorageManager
{
    /*
     * Prefix all our option names in the WordPress DB
     * with this value. Helps avoid naming conflicts.
     */
    const OPTION_PREFIX = "tubepress-";

    const VERSION = 226;

    const VERSION_OPTION_NAME = 'version';

    /**
     * Constructor. Until I can come up with a better way to validate options, this is gonna be how we
     * check to make sure that the db is initialized.
     */
    public function __construct()
    {
        $needToInit = false;

        if ($this->exists(self::VERSION_OPTION_NAME)) {
            $version = $this->get(self::VERSION_OPTION_NAME);
            if (!is_numeric($version) || $version < self::VERSION) {
                $needToInit = true;
            }
        } else {
            $this->create(self::VERSION_OPTION_NAME, self::VERSION);
            $needToInit = true;
        }

        if ($needToInit) {
            $this->init();
            $this->setOption(self::VERSION_OPTION_NAME, self::VERSION);
        }
    }

    /**
     * Creates an option in storage
     *
     * @param unknown_type $optionName  The name of the option to create
     * @param unknown_type $optionValue The default value of the new option
     *
     * @return void
     */
    protected function create($optionName, $optionValue)
    {
        add_option(self::OPTION_PREFIX . $optionName, $optionValue);
    }

    /**
     * Deletes an option from storage
     *
     * @param unknown_type $optionName The name of the option to delete
     *
     * @return void
     */
    protected function delete($optionName)
    {
        delete_option(self::OPTION_PREFIX . $optionName);
    }

    /**
     * Determines if an option exists
     *
     * @param string $optionName The name of the option in question
     *
     * @return boolean True if the option exists, false otherwise
     */
    public function exists($optionName)
    {
        return get_option(self::OPTION_PREFIX . $optionName) !== false;
    }

    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return unknown_type The option's value
     */
    public function get($optionName)
    {
        return get_option(self::OPTION_PREFIX . $optionName);
    }

    /**
     * Sets an option to a new value, without validation
     *
     * @param string       $optionName  The name of the option to update
     * @param unknown_type $optionValue The new option value
     *
     * @return void
     */
    protected function setOption($optionName, $optionValue)
    {
        update_option(self::OPTION_PREFIX . $optionName, $optionValue);
    }
}
