<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_wordpress_impl_options_WpPersistence implements tubepress_spi_options_PersistenceBackendInterface
{
    /*
     * Prefix all our option names in the WordPress DB
     * with this value. Helps avoid naming conflicts.
     */
    private static $_optionPrefix = 'tubepress-';

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * {@inheritdoc}
     */
    public function createEach(array $optionNamesToValuesMap)
    {
        $existingOptions = array_keys($this->fetchAllCurrentlyKnownOptionNamesToValues());
        $incomingOptions = array_keys($optionNamesToValuesMap);
        $newOptionNames  = array_diff($incomingOptions, $existingOptions);
        $toCreate        = array();
        foreach ($newOptionNames as $newOptionName) {

            $toCreate[$newOptionName] = $optionNamesToValuesMap[$newOptionName];
        }

        foreach ($toCreate as $missingOptionName => $defaultValue) {

            $this->_wpFunctions->add_option(self::$_optionPrefix . $missingOptionName, $defaultValue);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAllCurrentlyKnownOptionNamesToValues()
    {
        $allOptions           = $this->_wpFunctions->wp_load_alloptions();
        $allOptionNames       = array_keys($allOptions);
        $tubePressOptionNames = array_filter($allOptionNames, array($this, '__onlyPrefixedWithTubePress'));
        $toReturn             = array_intersect_key($allOptions, array_flip($tubePressOptionNames));

        foreach ($toReturn as $prefixedName => $value) {

            $unprefixedName            = str_replace(self::$_optionPrefix, '', $prefixedName);
            $toReturn[$unprefixedName] = $toReturn[$prefixedName];

            unset($toReturn[$prefixedName]);
        }

        return $toReturn;
    }

    public function __onlyPrefixedWithTubePress($key)
    {
        return strpos("$key", self::$_optionPrefix) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(array $optionNamesToValues)
    {
        foreach ($optionNamesToValues as $optionName => $optionValue) {

            $this->_wpFunctions->update_option(self::$_optionPrefix . $optionName, $optionValue);
        }

        /*
         * WordPress API is silly.
         *
         * http://codex.wordpress.org/Function_Reference/update_option
         */
        return null;
    }
}
