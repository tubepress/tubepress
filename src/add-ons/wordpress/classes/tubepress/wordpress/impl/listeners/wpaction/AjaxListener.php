<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wpaction_AjaxListener
{
    /**
     * @var tubepress_api_http_AjaxInterface
     */
    private $_ajaxHandler;

    /**
     * @var bool
     */
    private $_testMode = false;

    public function __construct(tubepress_api_http_AjaxInterface $ajaxHandler)
    {
        $this->_ajaxHandler = $ajaxHandler;
    }

    public function onAction_ajax(tubepress_api_event_EventInterface $event)
    {
        $this->_ajaxHandler->handle();

        if (!$this->_testMode) {

            exit;
        }
    }

    /**
     * DO NOT CALL THIS OUTSIDE OF TESTING.
     */
    public function __enabledTestMode()
    {
        $this->_testMode = true;
    }
}
