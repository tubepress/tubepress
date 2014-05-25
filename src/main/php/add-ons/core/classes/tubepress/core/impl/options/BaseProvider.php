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
class tubepress_core_impl_options_BaseProvider implements tubepress_core_api_options_ProviderInterface
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
     * @var tubepress_core_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_core_api_options_EasyProviderInterface
     */
    private $_delegate;

    public function __construct(tubepress_core_api_options_EasyProviderInterface $delegate,
                                tubepress_core_api_translation_TranslatorInterface $translator,
                                tubepress_core_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_api_util_LangUtilsInterface              $langUtils)
    {
        $this->_delegate        = $delegate;
        $this->_translator      = $translator;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_langUtils       = $langUtils;
    }

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
        $unfiltered = null;

        if (!isset($this->_cacheOfOptionNamesToFixedAcceptableValues)) {

            $this->_cacheOfOptionNamesToFixedAcceptableValues = $this->_delegate->getMapOfOptionNamesToFixedAcceptableValues();
        }

        if (isset($this->_cacheOfOptionNamesToFixedAcceptableValues[$optionName])) {

            $unfiltered = $this->_cacheOfOptionNamesToFixedAcceptableValues[$optionName];

        } else {

            if (!isset($this->_cacheOfOptionNamesWithDynamicAcceptableValues)) {

                $this->_cacheOfOptionNamesWithDynamicAcceptableValues = $this->_delegate->getOptionNamesWithDynamicDiscreteAcceptableValues();
            }

            if (in_array($optionName, $this->_cacheOfOptionNamesWithDynamicAcceptableValues)) {

                $unfiltered = $this->_delegate->getDynamicDiscreteAcceptableValuesForOption($optionName);
            }
        }

        return $this->_dispatchAndGetResult(

            $optionName,
            $unfiltered,
            tubepress_core_api_const_event_EventNames::OPTION_GET_DISCRETE_ACCEPTABLE_VALUES
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     */
    public function getDefaultValue($optionName)
    {
        $this->_primeDefaultValuesCache();

        $unfiltered = $this->_cacheOfOptionNamesToDefaultValues[$optionName];

        return $this->_dispatchAndGetResult(

            $optionName,
            $unfiltered,
            tubepress_core_api_const_event_EventNames::OPTION_GET_DEFAULT_VALUE
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     */
    public function getDescription($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesToDescriptions)) {

            $this->_cacheOfOptionNamesToDescriptions = $this->_delegate->getMapOfOptionNamesToUntranslatedDescriptions();
        }

        return $this->_dispatchText(

            $optionName,
            $this->_cacheOfOptionNamesToDescriptions,
            tubepress_core_api_const_event_EventNames::OPTION_GET_DESCRIPTION
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less.
     */
    public function getLabel($optionName)
    {
        if (!isset($this->_cacheOfOptionNamesToLabels)) {

            $this->_cacheOfOptionNamesToLabels = $this->_delegate->getMapOfOptionNamesToUntranslatedLabels();
        }

        return $this->_dispatchText(

            $optionName,
            $this->_cacheOfOptionNamesToLabels,
            tubepress_core_api_const_event_EventNames::OPTION_GET_LABEL
        );
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
        if (!$this->hasOption($optionName)) {

            return sprintf('No option with name "%s".', $optionName);                          //>(translatable)<
        }

        if (!isset($this->_cacheOfOptionNamesToRegexes)) {

            $this->_cacheOfOptionNamesToRegexes = $this->_delegate->getMapOfOptionNamesToValidValueRegexes();

            $positiveIntegers = $this->_delegate->getOptionNamesOfPositiveIntegers();
            foreach ($positiveIntegers as $positiveInteger) {

                $this->_cacheOfOptionNamesToRegexes[$positiveInteger] = self::$_regexPositiveInteger;
            }

            $negativeIntegers = $this->_delegate->getOptionNamesOfNonNegativeIntegers();
            foreach ($negativeIntegers as $negativeInteger) {

                $this->_cacheOfOptionNamesToRegexes[$negativeInteger] = self::$_regexNonNegativeInteger;
            }
        }

        if (isset($this->_cacheOfOptionNamesToRegexes[$optionName])) {

            if (preg_match_all($this->_cacheOfOptionNamesToRegexes[$optionName], (string) $candidate, $matches) >= 1 && $matches[0][0] === (string) $candidate) {

                return null;
            }

            return sprintf('Invalid value supplied for "%s".', $this->_translator->_($this->getLabel($optionName)));      //>(translatable)<
        }

        if ($this->isBoolean($optionName)) {

            if (is_bool($candidate)) {

                return null;
            }

            return sprintf('"%s" can only be "true" or "false". You supplied "%s".', $this->_translator->_($this->getLabel($optionName)), $candidate);  //>(translatable)<
        }

        $acceptableValues = $this->getDiscreteAcceptableValues($optionName);

        if ($acceptableValues !== null) {

            if ($this->_langUtils->isAssociativeArray($acceptableValues)) {

                $values = array_keys($acceptableValues);

            } else {

                $values = array_values($acceptableValues);
            }

            if (in_array($candidate, $values)) {

                return null;
            }

            return sprintf('"%s" must be one of "%s". You supplied "%s".',                               //>(translatable)<
                $this->_translator->_($this->getLabel($optionName)), implode(', ', $values), $candidate);
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

            $this->_cacheOfOptionNamesUnsuitableForShortcode = $this->_delegate->getOptionNamesThatCannotBeSetViaShortcode();
        }

        return !in_array($optionName, $this->_cacheOfOptionNamesUnsuitableForShortcode);
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

            $this->_cacheOfOptionNamesUnsuitableForPersistence = $this->_delegate->getOptionsNamesThatShouldNotBePersisted();
        }

        return !in_array($optionName, $this->_cacheOfOptionNamesUnsuitableForPersistence);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     */
    public function isProOnly($optionName)
    {
        if (!isset($this->_cacheOfProOptionNames)) {

            $this->_cacheOfProOptionNames = $this->_delegate->getAllProOptionNames();
        }

        return in_array($optionName, $this->_cacheOfProOptionNames);
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





    private function _primeDefaultValuesCache()
    {
        if (!isset($this->_cacheOfOptionNamesToDefaultValues)) {

            $this->_cacheOfOptionNamesToDefaultValues = $this->_delegate->getMapOfOptionNamesToDefaultValues();
        }
    }

    private function _dispatchText($optionName, array $arrayToSearch, $eventName)
    {
        if (isset($arrayToSearch[$optionName])) {

            $unfiltered = $arrayToSearch[$optionName];

        } else {

            $unfiltered = null;
        }

        return $this->_dispatchAndGetResult($optionName, $unfiltered, $eventName);
    }

    private function _dispatchAndGetResult($optionName, $value, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($value);

        $this->_eventDispatcher->dispatch($eventName . ".$optionName", $event);

        return $event->getSubject();
    }
}
