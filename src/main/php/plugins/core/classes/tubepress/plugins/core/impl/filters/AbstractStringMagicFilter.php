<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Performs filtering on potentially malicious or typo'd string input.
 */
abstract class tubepress_plugins_core_impl_filters_AbstractStringMagicFilter
{
    protected function _magic(tubepress_api_event_TubePressEvent $event)
    {
        $value = $event->getSubject();

        /** If it's an array, send each element through the filter. */
        if (is_array($value)) {

            foreach ($value as $key => $subValue) {

                $subEvent = new tubepress_api_event_TubePressEvent($subValue);
                $subEvent->setArgument('optionName', $key);

                $this->_magic($subEvent);

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
