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

class tubepress_options_impl_Context implements tubepress_api_options_ContextInterface
{
    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_ephemeralOptions = array();

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * Constructor.
     */
    public function __construct(tubepress_api_options_PersistenceInterface   $persistence,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_api_options_ReferenceInterface     $reference)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_persistence     = $persistence;
        $this->_optionReference = $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function get($optionName)
    {
        if (array_key_exists($optionName, $this->_ephemeralOptions)) {

            return $this->_ephemeralOptions[$optionName];
        }

        try {

            return $this->_persistence->fetch($optionName);

        } catch (InvalidArgumentException $e) {

            if ($this->_optionReference->optionExists($optionName) &&
                !$this->_optionReference->isMeantToBePersisted($optionName)) {

                return null;
            }

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEphemeralOptions()
    {
        return $this->_ephemeralOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function setEphemeralOption($optionName, $optionValue)
    {
        $errors = $this->getErrors($optionName, $optionValue);

        if (count($errors) === 0) {

            $this->_ephemeralOptions[$optionName] = $optionValue;

            return null;
        }

        return $errors[0];
    }

    /**
     * {@inheritdoc}
     */
    public function setEphemeralOptions(array $customOpts)
    {
        $toReturn                = array();
        $this->_ephemeralOptions = array();

        foreach ($customOpts as $name => $value) {

            $error = $this->setEphemeralOption($name, $value);

            if ($error !== null) {

                $toReturn[] = $error;
            }
        }

        return $toReturn;
    }

    protected function getErrors($optionName, &$optionValue)
    {
        $externallyCleanedValue = $this->_dispatchForExternalInput($optionName, $optionValue);

        $event = $this->_dispatchForOptionSet(
            $optionName,
            $externallyCleanedValue,
            array(),
            tubepress_api_event_Events::OPTION_SET . '.' . $optionName
        );

        $event = $this->_dispatchForOptionSet(
            $optionName,
            $event->getArgument('optionValue'),
            $event->getSubject(),
            tubepress_api_event_Events::OPTION_SET
        );

        $optionValue = $event->getArgument('optionValue');

        return $event->getSubject();
    }

    private function _dispatchForExternalInput($optionName, $optionValue)
    {
        $event = $this->_eventDispatcher->newEventInstance($optionValue, array(

            'optionName' => $optionName,
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT, $event);

        return $event->getSubject();
    }

    private function _dispatchForOptionSet($optionName, $optionValue, array $errors, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($errors, array(

            'optionName'  => $optionName,
            'optionValue' => $optionValue,
        ));

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event;
    }
}
