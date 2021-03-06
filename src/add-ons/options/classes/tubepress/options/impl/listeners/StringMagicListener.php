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

/**
 * Performs filtering on potentially malicious or typo'd string input.
 */
class tubepress_options_impl_listeners_StringMagicListener
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionsReference;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                tubepress_api_options_ReferenceInterface     $reference)
    {
        $this->_eventDispatcher  = $eventDispatcher;
        $this->_optionsReference = $reference;
    }

    public function onExternalInput(tubepress_api_event_EventInterface $event)
    {
        $value = $event->getSubject();
        $name  = $event->getArgument('optionName');

        $this->_magic($name, $value);

        $event->setSubject($value);
    }

    public function onSet(tubepress_api_event_EventInterface $event)
    {
        $value = $event->getArgument('optionValue');
        $name  = $event->getArgument('optionName');

        $this->_magic($name, $value);

        $event->setArgument('optionValue', $value);
    }

    private function _magic($name, &$value)
    {
        /* If it's an array, send each element through the filter. */
        if (is_array($value)) {

            foreach ($value as $key => $subValue) {

                $this->_magic($name, $subValue);
                $value[$key] = $subValue;
            }
        }

        /* We're only interested in strings. */
        if (!is_string($value)) {

            return;
        }

        $value = trim($value);

        if ($this->_optionsReference->optionExists($name) &&
            !$this->_optionsReference->isHtmlAllowed($name)) {

            $value = htmlspecialchars($value, ENT_NOQUOTES);
        }

        $value = $this->_booleanMagic($value);
    }

    //http://php.net/manual/en/language.types.boolean.php
    private function _booleanMagic($value)
    {
        if (strcasecmp($value, 'false') === 0) {

            return false;
        }

        if (strcasecmp($value, 'true') === 0) {

            return true;
        }

        return $value;
    }
}
