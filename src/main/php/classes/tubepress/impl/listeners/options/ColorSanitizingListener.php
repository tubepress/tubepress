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
 *
 */
class tubepress_impl_listeners_options_ColorSanitizingListener
{
    private $_applicableOptionNames = array();

    public function __construct(array $applicableOptionNames)
    {
        $this->_applicableOptionNames = $applicableOptionNames;
    }

    public function onPreValidationOptionSet(tubepress_api_event_EventInterface $event)
    {
        $name = $event->getArgument('optionName');

        if (!in_array($name, $this->_applicableOptionNames)) {

            return;
        }

        $value = $event->getSubject();

        $value = $this->_stripLeadingHash($value);

        $event->setSubject($value);
    }

    private function _stripLeadingHash($value)
    {
        if (!tubepress_impl_util_StringUtils::startsWith($value, '#')) {

            return $value;
        }

        return ltrim($value, '#');
    }
}