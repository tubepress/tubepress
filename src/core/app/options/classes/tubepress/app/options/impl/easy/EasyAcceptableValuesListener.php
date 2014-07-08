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
class tubepress_app_options_impl_easy_EasyAcceptableValuesListener
{
    /**
     * @var array
     */
    private $_acceptableValues;

    public function __construct(array $acceptableValues)
    {
        $this->_acceptableValues = $acceptableValues;
    }

    public function onAcceptableValues(tubepress_lib_event_api_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $event->setSubject(array_merge($current, $this->_acceptableValues));
    }
}