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
class tubepress_core_impl_listeners_LtrimSubjectListener
{
    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var string
     */
    private $_needle;

    public function __construct(tubepress_api_util_StringUtilsInterface $stringUtils,
                                $needle)
    {
        $this->_stringUtils = $stringUtils;
        $this->_needle      = "$needle";
    }

    public function execute(tubepress_core_api_event_EventInterface $event)
    {
        $value = $event->getSubject();

        if (!is_string($value) || !$this->_stringUtils->startsWith($value, $this->_needle)) {

            return;
        }

        $event->setSubject(ltrim($value, $this->_needle));
    }
}