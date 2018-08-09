<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_options_impl_DispatchingReference implements tubepress_api_options_ReferenceInterface
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_options_ReferenceInterface[]
     */
    private $_delegateReferences = array();

    /**
     * @var array
     */
    private $_nameToReferenceMap;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptionNames()
    {
        $this->_initCache();

        return array_keys($this->_nameToReferenceMap);
    }

    /**
     * {@inheritdoc}
     */
    public function optionExists($optionName)
    {
        $this->_initCache();

        return array_key_exists($optionName, $this->_nameToReferenceMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($optionName, $propertyName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->getProperty($optionName, $propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperty($optionName, $propertyName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->hasProperty($optionName, $propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyAsBoolean($optionName, $propertyName)
    {
        return (bool) $this->getProperty($optionName, $propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getDefaultValue($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_api_event_Events::OPTION_DEFAULT_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDescription($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getUntranslatedDescription($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_api_event_Events::OPTION_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedLabel($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        $raw = $this->_nameToReferenceMap[$optionName]->getUntranslatedLabel($optionName);

        return $this->_dispatchEventAndReturnSubject($optionName, $raw,
            tubepress_api_event_Events::OPTION_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isAbleToBeSetViaShortcode($optionName);
    }

    /**
     * {@inheritdoc}
     */
    public function isBoolean($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isBoolean($optionName);
    }

    /**
     * {@inheritdoc}
     */
    public function isMeantToBePersisted($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
        return $this->_nameToReferenceMap[$optionName]->isMeantToBePersisted($optionName);
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly($optionName)
    {
        $this->_assertExists($optionName);

        /* @noinspection PhpUndefinedMethodInspection */
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
            'optionName' => $optionName,
        ));

        $this->_eventDispatcher->dispatch("$eventName.$optionName", $event);

        return $event->getSubject();
    }
}
