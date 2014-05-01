<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Implementation of tubepress_api_options_PersistenceInterface that uses the
 * regular WordPress options API.
 */
class tubepress_addons_wordpress_impl_options_PersistenceBackend implements tubepress_api_options_PersistenceBackendInterface
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
    public function createEach(array $optionNamesToValuesMap)
    {
        $wordPressFunctionWrapperService =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $existingOptions = array_keys($this->fetchAllCurrentlyKnownOptionNamesToValues());
        $incomingOptions = array_keys($optionNamesToValuesMap);
        $newOptionNames  = array_diff($incomingOptions, $existingOptions);
        $toCreate        = array();
        foreach ($newOptionNames as $newOptionName) {

            $toCreate[$newOptionName] = $optionNamesToValuesMap[$newOptionName];
        }

        foreach ($toCreate as $missingOptionName => $defaultValue) {

            $wordPressFunctionWrapperService->add_option(self::$_optionPrefix . $missingOptionName, $defaultValue);
        }
    }

    /**
     * @return array An associative array of all option names to values.
     */
    public function fetchAllCurrentlyKnownOptionNamesToValues()
    {
        global $wpdb;

        /** @noinspection PhpUndefinedMethodInspection */
        $raw = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'tubepress-%'");

        if (!$raw || !is_array($raw)) {

            return array();
        }

        $toReturn = array();

        foreach ($raw as $option) {

            $toReturn[str_replace(self::$_optionPrefix, '', $option->option_name)] = $option->option_value;
        }

        return $toReturn;
    }

    /**
     * @param array $optionNamesToValues An associative array of option names to values.
     *
     * @return null|string Null if the save succeeded and all queued options were saved, otherwise a string error message.
     */
    public function saveAll(array $optionNamesToValues)
    {
        $wordPressFunctionWrapperService =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        foreach ($optionNamesToValues as $optionName => $optionValue) {

            $wordPressFunctionWrapperService->update_option(self::$_optionPrefix . $optionName, $optionValue);
        }

        /**
         * WordPress API is silly.
         *
         * http://codex.wordpress.org/Function_Reference/update_option
         */
        return null;
    }
}
