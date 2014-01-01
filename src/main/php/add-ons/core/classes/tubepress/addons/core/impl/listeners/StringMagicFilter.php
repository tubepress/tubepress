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
 * Performs filtering on potentially malicious or typo'd string input.
 */
class tubepress_addons_core_impl_listeners_StringMagicFilter
{
    public function magic(tubepress_api_event_EventInterface $event)
    {
        $value = $event->getSubject();

        /** If it's an array, send each element through the filter. */
        if (is_array($value)) {

            foreach ($value as $key => $subValue) {

                $subEvent = new tubepress_spi_event_EventBase($subValue);
                $subEvent->setArgument('optionName', $key);

                $this->magic($subEvent);

                $value[$key] = $subEvent->getSubject();
            }

            $event->setSubject($value);

            return;
        }

        /** We're only interested in strings. */
        if (! is_string($value)) {

            return;
        }

        $toReturn = trim($value);
        $toReturn = htmlspecialchars($toReturn, ENT_NOQUOTES);
        $toReturn = tubepress_impl_util_StringUtils::stripslashes_deep($toReturn);
        $toReturn = $this->_booleanMagic($toReturn);

        $event->setSubject($toReturn);
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
