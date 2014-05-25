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
 * Performs validation on option values
 */
class tubepress_core_impl_options_Provider implements tubepress_core_api_options_ProviderInterface
{
    /**
     * @var tubepress_core_api_options_ProviderInterface[]
     */
    private $_pluggableOptionProviders;

    /**
     * @var string[]
     */
    private $_allOptionNames;

    /**
     * @var array
     */
    private $_cacheOptionNameToProvider = array();

    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     */
    public function getAllOptionNames()
    {
        return $this->_allOptionNames;
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An array of acceptable values that this option can take. May be null or empty.
     */
    public function getDiscreteAcceptableValues($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return array();
        }

        return $provider->getDiscreteAcceptableValues($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     */
    public function getDefaultValue($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return null;
        }

        return $provider->getDefaultValue($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     */
    public function getDescription($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return '';
        }

        return $provider->getDescription($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less.
     */
    public function getLabel($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return '';
        }

        return $provider->getLabel($optionName);
    }

    /**
     * Gets the failure message of a name/value pair that has failed validation.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return mixed Null if the option passes validation, otherwise a string failure message.
     */
    public function getProblemMessage($optionName, $candidate)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return sprintf('No option provider is aware of option named "%s"', $optionName);
        }

        return $provider->getProblemMessage($optionName, $candidate);
    }

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     */
    public function hasOption($optionName)
    {
        return in_array((string) $optionName, $this->_allOptionNames);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return false;
        }

        return $provider->isAbleToBeSetViaShortcode($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     */
    public function isBoolean($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return false;
        }

        return $provider->isBoolean($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     */
    public function isMeantToBePersisted($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return false;
        }

        return $provider->isMeantToBePersisted($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     */
    public function isProOnly($optionName)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return false;
        }

        return $provider->isProOnly($optionName);
    }

    /**
     * Validates an option value.
     *
     * @param string $optionName The option name
     * @param mixed  $candidate  The candidate option value
     *
     * @return boolean True if the option name exists and the value supplied is valid. False otherwise.
     */
    public function isValid($optionName, $candidate)
    {
        $provider = $this->_findProviderOfOption($optionName);

        if ($provider === null) {

            return false;
        }

        return $provider->isValid($optionName, $candidate);
    }

    /**
     * @param tubepress_core_api_options_ProviderInterface[] $optionProviders
     */
    public function setAddonOptionProviders(array $optionProviders)
    {
        $this->_pluggableOptionProviders = $optionProviders;
    }

    /**
     * @param string[] $optionNames
     */
    public function setRegisteredOptionNames(array $optionNames)
    {
        $this->_allOptionNames = $optionNames;
    }

    /**
     * @param $optionName
     *
     * @return tubepress_core_api_options_ProviderInterface
     *
     * @throws RuntimeException
     */
    private function _findProviderOfOption($optionName)
    {
        if (!isset($this->_cacheOptionNameToProvider[$optionName])) {

            foreach ($this->_pluggableOptionProviders as $optionProvider) {

                if ($optionProvider->hasOption($optionName)) {

                    $this->_cacheOptionNameToProvider[$optionName] = $optionProvider;
                    break;
                }
            }
        }

        if (!isset($this->_cacheOptionNameToProvider[$optionName])) {

            return null;
        }

        return $this->_cacheOptionNameToProvider[$optionName];
    }
}
