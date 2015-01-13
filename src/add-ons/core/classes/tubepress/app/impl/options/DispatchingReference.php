<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_app_impl_options_DispatchingReference implements tubepress_app_api_options_ReferenceInterface
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_app_api_options_ReferenceInterface[]
     */
    private $_delegateReferences = array();

    /**
     * @var array
     */
    private $_nameToReferenceMap;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Fetch all the option names known to TubePress.
     *
     * @return string[]
     *
     * @api
     * @since 4.0.0
     */
    public function getAllOptionNames()
    {
        $this->_initCache();

        return array_keys($this->_nameToReferenceMap);
    }

    /**
     * @param string $optionName The option name.
     *
     * @return bool True if an option with the given name exists, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function optionExists($optionName)
    {
        $this->_initCache();

        return array_key_exists($optionName, $this->_nameToReferenceMap);
    }

    /**
     * Get a property for the given option.
     *
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    public function getProperty($optionName, $propertyName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->getProperty($optionName, $propertyName);
    }

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool True if this object contains a property with the given name, false otherwise.
     *
     * @throws InvalidArgumentException If the option name does not exist
     *
     * @api
     * @since 4.0.0
     */
    public function hasProperty($optionName, $propertyName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->hasProperty($optionName, $propertyName);
    }

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool The property value as converted to boolean.
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    public function getPropertyAsBoolean($optionName, $propertyName)
    {
        return (bool) $this->getProperty($optionName, $propertyName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getDefaultValue($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getDefaultValue($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_app_api_event_Events::OPTION_DEFAULT_VALUE);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getUntranslatedDescription($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getUntranslatedDescription($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_app_api_event_Events::OPTION_DESCRIPTION);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getUntranslatedLabel($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getUntranslatedLabel($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_app_api_event_Events::OPTION_LABEL);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isAbleToBeSetViaShortcode($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isBoolean($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isBoolean($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isMeantToBePersisted($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isMeantToBePersisted($optionName);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isProOnly($optionName)
    {
        $this->_assertExists($optionName);

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isProOnly($optionName);
    }

    public function setReferences(array $references)
    {
        $this->_delegateReferences = $references;
    }

    private function _initCache()
    {
        if (isset($this->_nameToReferenceMap)) {

            return;
        }

        $this->_nameToReferenceMap = array();

        foreach ($this->_delegateReferences as $delegateReference) {

            $allOptions = $delegateReference->getAllOptionNames();

            foreach ($allOptions as $optionName) {

                $this->_nameToReferenceMap[$optionName] = $delegateReference;
            }
        }
    }

    private function _assertExists($optionName)
    {
        if (!$this->optionExists($optionName)) {

            throw new InvalidArgumentException("$optionName is not a known option");
        }
    }

    private function _dispatchEventAndReturnSubject($optionName, $value, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($value, array(
            'optionName' => $optionName
        ));

        $this->_eventDispatcher->dispatch("$eventName.$optionName", $event);

        return $event->getSubject();
    }
}
