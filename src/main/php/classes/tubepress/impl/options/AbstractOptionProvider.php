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
abstract class tubepress_impl_options_AbstractOptionProvider implements tubepress_spi_options_OptionProvider
{
    private static $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
    private static $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';

    /**
     * @var string[]
     */
    private $_cacheOfProOptionNames;

    /**
     * @var array
     */
    private $_cacheOfOptionNamesToLabels;

    /**
     * @var array
     */
    private $_cacheOfOptionNamesToDescriptions;

    /**
     * @var array
     */
    private $_cacheOfOptionNamesToDefaultValues;

    /**
     * @var array
     */
    private $_cacheOfOptionNamesToRegexes;

    /**
     * @var string[]
     */
    private $_cacheOfOptionNamesUnsuitableForShortcode;

    /**
     * @var string[]
     */
    private $_cacheOfOptionNamesUnsuitableForPersistence;

    /**
     * @var array
     */
    private $_cacheOfOptionNamesToFixedAcceptableValues;

    /**
     * @var string[]
     */
    private $_cacheOfOptionNamesWithDynamicAcceptableValues;

    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     */
    public function getAllOptionNames()
    {
        $this->_primeDefaultValuesCache();

        return array_keys($this->_cacheOfOptionNamesToDefaultValues);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array of values to untranslated descriptions that the given
     *               option can accept. May be null if the option does not support discrete values.
     */
    public function getDiscreteAcceptableValues($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesToFixedAcceptableValues)) {

            $this->_cacheOfOptionNamesToFixedAcceptableValues = $this->getMapOfOptionNamesToFixedAcceptableValues();
        }

        if (isset($this->_cacheOfOptionNamesToFixedAcceptableValues[$optionName])) {

            return $this->_cacheOfOptionNamesToFixedAcceptableValues[$optionName];
        }

        if (!isset($this->_cacheOfOptionNamesWithDynamicAcceptableValues)) {

            $this->_cacheOfOptionNamesWithDynamicAcceptableValues = $this->getOptionNamesWithDynamicDiscreteAcceptableValues();
        }

        if (isset($this->_cacheOfOptionNamesWithDynamicAcceptableValues[$optionName])) {

            return $this->getDynamicDiscreteAcceptableValuesForOption($optionName);
        }

        return null;
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     */
    public function getDefaultValue($optionName)
    {
        $this->_primeDefaultValuesCache();

        return $this->_cacheOfOptionNamesToDefaultValues[$optionName];
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     */
    public function getDescription($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesToDescriptions)) {

            $this->_cacheOfOptionNamesToDescriptions = $this->getMapOfOptionNamesToUntranslatedDescriptions();
        }

        return $this->_inArrayOrNull($optionName, $this->_cacheOfOptionNamesToDescriptions);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less.
     */
    public function getLabel($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesToLabels)) {

            $this->_cacheOfOptionNamesToLabels = $this->getMapOfOptionNamesToUntranslatedLabels();
        }

        return $this->_inArrayOrNull($optionName, $this->_cacheOfOptionNamesToLabels);
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
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        if (!$this->hasOption($optionName)) {

            return sprintf('No option with name "%s".', $optionName);                          //>(translatable)<
        }

        if (!isset($this->_cacheOfOptionNamesToRegexes)) {

            $this->_cacheOfOptionNamesToRegexes = $this->getMapOfOptionNamesToValidValueRegexes();

            $positiveIntegers = $this->getOptionNamesOfPositiveIntegers();
            foreach ($positiveIntegers as $positiveInteger) {

                $this->_cacheOfOptionNamesToRegexes[$positiveInteger] = self::$_regexPositiveInteger;
            }

            $negativeIntegers = $this->getOptionNamesOfNonNegativeIntegers();
            foreach ($negativeIntegers as $negativeInteger) {

                $this->_cacheOfOptionNamesToRegexes[$negativeInteger] = self::$_regexNonNegativeInteger;
            }
        }

        if (isset($this->_cacheOfOptionNamesToRegexes[$optionName])) {

            if (preg_match_all($this->_cacheOfOptionNamesToRegexes[$optionName], (string) $candidate, $matches) >= 1 && $matches[0][0] === (string) $candidate) {

                return null;
            }

            return sprintf('Invalid value supplied for "%s".', $messageService->_($this->getLabel($optionName)));      //>(translatable)<
        }

        if ($this->isBoolean($optionName)) {

            if (is_bool($candidate)) {

                return null;
            }

            return sprintf('"%s" can only be "true" or "false". You supplied "%s".', $optionName, $candidate);  //>(translatable)<
        }

        $acceptableValues = $this->getDiscreteAcceptableValues($optionName);

        if ($acceptableValues !== null) {

            if (tubepress_impl_util_LangUtils::isAssociativeArray($acceptableValues)) {

                $values = array_keys($acceptableValues);

            } else {

                $values = array_values($acceptableValues);
            }

            if (in_array($candidate, $values)) {

                return null;
            }

            return sprintf('"%s" must be one of "%s". You supplied "%s".',                               //>(translatable)<
                $messageService->_($this->getLabel($optionName)), implode(', ', $values), $candidate);
        }

        return null;
    }

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     */
    public function hasOption($optionName)
    {
        $this->_primeDefaultValuesCache();

        return array_key_exists($optionName, $this->_cacheOfOptionNamesToDefaultValues);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesUnsuitableForShortcode)) {

            $this->_cacheOfOptionNamesUnsuitableForShortcode = $this->getOptionNamesThatCannotBeSetViaShortcode();
        }

        return !isset($this->_cacheOfOptionNamesUnsuitableForShortcode[$optionName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     */
    public function isBoolean($optionName)
    {
        $this->_primeDefaultValuesCache();

        return is_bool($this->_cacheOfOptionNamesToDefaultValues[$optionName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     */
    public function isMeantToBePersisted($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesUnsuitableForPersistence)) {

            $this->_cacheOfOptionNamesUnsuitableForPersistence = $this->getOptionsNamesThatShouldNotBePersisted();
        }

        return !isset($this->_cacheOfOptionNamesUnsuitableForPersistence[$optionName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     */
    public function isProOnly($optionName)
    {
        if (!isset($this->_cacheOfProOptionNames)) {

            $this->_cacheOfProOptionNames = $this->getAllProOptionNames();
        }

        return isset($this->_cacheOfProOptionNames[$optionName]);
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
        return $this->getProblemMessage($optionName, $candidate) === null;
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionNamesThatCannotBeSetViaShortcode()
    {
        //override point
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionsNamesThatShouldNotBePersisted()
    {
        //override point
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    protected function getMapOfOptionNamesToFixedAcceptableValues()
    {
        //override point
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     */
    protected function getOptionNamesWithDynamicDiscreteAcceptableValues()
    {
        //override point
        return array();
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     */
    protected function getDynamicDiscreteAcceptableValuesForOption($optionName)
    {
        //override point
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent positive integers.
     */
    protected function getOptionNamesOfPositiveIntegers()
    {
        //override point
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent non-negative integers.
     */
    protected function getOptionNamesOfNonNegativeIntegers()
    {
        //override point
        return array();
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    protected function getAllProOptionNames()
    {
        //override point
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    protected function getMapOfOptionNamesToValidValueRegexes()
    {
        //override point
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected abstract function getMapOfOptionNamesToUntranslatedLabels();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected abstract function getMapOfOptionNamesToUntranslatedDescriptions();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected abstract function getMapOfOptionNamesToDefaultValues();

    private function _inArrayOrNull($key, array $array)
    {
        if (isset($array[$key])) {

            return $array[$key];
        }

        return null;
    }

    private function _primeDefaultValuesCache()
    {
        if (!isset($this->_cacheOfOptionNamesToDefaultValues)) {

            $this->_cacheOfOptionNamesToDefaultValues = $this->getMapOfOptionNamesToDefaultValues();
        }
    }
}
