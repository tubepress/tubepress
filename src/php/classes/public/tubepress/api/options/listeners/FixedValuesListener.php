<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Applies a set of fixed values to the acceptable values of an option.
 *
 * @api
 * @since 4.0.0
 */
class tubepress_api_options_listeners_FixedValuesListener
{
    /**
     * @var array
     */
    private $_acceptableValues;

    public function __construct(array $acceptableValues)
    {
        $this->_acceptableValues = $acceptableValues;
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $event->setSubject(array_merge($current, $this->_acceptableValues));
    }
}