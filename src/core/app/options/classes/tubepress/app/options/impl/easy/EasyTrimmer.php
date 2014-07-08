<?php
/**
 * Copyright 2006  2014 TubePress LLC (http://tubepress.com)
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
class tubepress_app_options_impl_easy_EasyTrimmer
{
    private $_ltrim = false;

    private $_rtrim = false;

    /**
     * @var string
     */
    private $_charlist;

    public function __construct($charlist)
    {
        $this->_charlist = $charlist;
    }

    public function onOption(tubepress_lib_event_api_EventInterface $event)
    {
        $optionValue = $event->getArgument('optionValue');

        if (!is_scalar($optionValue)) {

            return;
        }

        if ($this->_ltrim) {

            $optionValue = ltrim($optionValue, $this->_charlist);

        } else if ($this->_rtrim) {

            $optionValue = rtrim($optionValue, $this->_charlist);

        } else {

            $optionValue = trim($optionValue, $this->_charlist);
        }

        $event->setArgument('optionValue', $optionValue);
    }

    public function setModeToLtrim()
    {
        $this->_ltrim = true;
    }

    public function setModeToRtrim()
    {
        $this->_rtrim = true;
    }
}