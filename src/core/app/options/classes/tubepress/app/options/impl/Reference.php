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
class tubepress_app_options_impl_Reference implements tubepress_app_options_api_ReferenceInterface
{
    /**
     * @var array
     */
    private $_labelMap;

    /**
     * @var array
     */
    private $_descriptionMap;

    /**
     * @var array
     */
    private $_defaultValueMap;

    /**
     * @var string[]
     */
    private $_doNotPersist;

    /**
     * @var string[]
     */
    private $_noShortcode;

    /**
     * @var string[]
     */
    private $_proNames;

    /**
     * @var tubepress_lib_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(array $valueMap,
                                array $labelMap,
                                array $descriptionMap,
                                array $doNotPersist,
                                array $noShortcode,
                                array $proNames,
                                tubepress_lib_event_api_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_defaultValueMap = $valueMap;
        $this->_labelMap        = $labelMap;
        $this->_descriptionMap  = $descriptionMap;
        $this->_doNotPersist    = $doNotPersist;
        $this->_noShortcode     = $noShortcode;
        $this->_proNames        = $proNames;
    }

    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     *
     * @api
     * @since 4.0.0
     */
    public function getAllOptionNames()
    {
        return array_keys($this->_defaultValueMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDefaultValue($optionName)
    {
        $raw = $this->_fromArrayOrNull($this->_defaultValueMap, $optionName);

        return $this->_dispatchAndGetResult(

            $optionName,
            $raw,
            tubepress_app_options_api_Constants::EVENT_OPTION_GET_DEFAULT_VALUE
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDescription($optionName)
    {
        $raw = $this->_fromArrayOrNull($this->_descriptionMap, $optionName);

        return $this->_dispatchAndGetResult(

            $optionName,
            $raw,
            tubepress_app_options_api_Constants::EVENT_OPTION_GET_DESCRIPTION
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedLabel($optionName)
    {
        $raw = $this->_fromArrayOrNull($this->_labelMap, $optionName);

        return $this->_dispatchAndGetResult(

            $optionName,
            $raw,
            tubepress_app_options_api_Constants::EVENT_OPTION_GET_LABEL
        );
    }

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function optionExists($optionName)
    {
        return array_key_exists($optionName, $this->_defaultValueMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        return !in_array($optionName, $this->_noShortcode);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isBoolean($optionName)
    {
        return $this->optionExists($optionName) && is_bool($this->_defaultValueMap[$optionName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     *
     * @api
     * @since 4.0.0
     */
    public function isMeantToBePersisted($optionName)
    {
        return !in_array($optionName, $this->_doNotPersist);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly($optionName)
    {
        return in_array($optionName, $this->_proNames);
    }

    private function _fromArrayOrNull(array $haystack, $needle)
    {
        if (isset($haystack[$needle])) {

            return $haystack[$needle];
        }

        return null;
    }

    private function _dispatchAndGetResult($optionName, $value, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($value, array(
            'optionName' => $optionName
        ));

        $this->_eventDispatcher->dispatch($eventName . ".$optionName", $event);

        return $event->getSubject();
    }
}
