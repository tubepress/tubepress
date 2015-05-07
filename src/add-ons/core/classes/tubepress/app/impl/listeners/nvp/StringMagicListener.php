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
 * Performs filtering on potentially malicious or typo'd string input.
 */
class tubepress_app_impl_listeners_nvp_StringMagicListener
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onExternalInput(tubepress_lib_api_event_EventInterface $event)
    {
        $value = $event->getSubject();

        $this->_magic($value);

        $event->setSubject($value);
    }

    public function onSet(tubepress_lib_api_event_EventInterface $event)
    {
        $value = $event->getArgument('optionValue');

        $this->_magic($value);

        $event->setArgument('optionValue', $value);
    }

    private function _magic(&$value)
    {
        /** If it's an array, send each element through the filter. */
        if (is_array($value)) {

            foreach ($value as $key => $subValue) {

                $this->_magic($subValue);
                $value[$key] = $subValue;
            }
        }

        /** We're only interested in strings. */
        if (! is_string($value)) {

            return;
        }

        $value = trim($value);
        $value = htmlspecialchars($value, ENT_NOQUOTES);
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
