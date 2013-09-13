<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Implementation of tubepress_spi_options_StorageManager that uses the
 * regular WordPress options API.
 */
class tubepress_addons_wordpress_impl_options_WordPressStorageManager extends tubepress_impl_options_AbstractStorageManager
{
    /*
     * Prefix all our option names in the WordPress DB
     * with this value. Helps avoid naming conflicts.
     */
    private static $_optionPrefix = "tubepress-";

    /**
     * Creates multiple options in storage.
     *
     * @param array $optionNamesToValuesMap An associative array of option names to option values. For each
     *                                      element in the array, we will call createIfNotExists($name, $value)
     *
     * @return void
     */
    public function createEachIfNotExists(array $optionNamesToValuesMap)
    {
        $allKnowOptionNames  = $this->_getAllOptionNames();
        $incomingOptionNames = array_keys($optionNamesToValuesMap);
        $missingOptionNames  = array_diff($incomingOptionNames, $allKnowOptionNames);

        if (count($missingOptionNames) === 0) {

            //common case
            return;
        }

        $wordPressFunctionWrapperService =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        foreach ($missingOptionNames as $missingOptionName) {

            $wordPressFunctionWrapperService->add_option(self::$_optionPrefix . $missingOptionName, $optionNamesToValuesMap[$missingOptionName]);
        }
    }

    /**
     * Retrieve the current value of an option
     *
     * @param string $optionName The name of the option
     *
     * @return mixed The option's value
     */
    public final function get($optionName)
    {
        $wordPressFunctionWrapperService =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

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
    protected final function saveValidatedOption($optionName, $optionValue)
    {
        $wordPressFunctionWrapperService =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $wordPressFunctionWrapperService->update_option(self::$_optionPrefix . $optionName, $optionValue);
    }

    /**
     * @return array All the option names currently in this storage manager.
     */
    private final function _getAllOptionNames()
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
