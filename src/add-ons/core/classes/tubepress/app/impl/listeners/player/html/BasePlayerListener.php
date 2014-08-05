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
 * HTML-generation command that implements the "solo" player command.
 */
class tubepress_app_impl_listeners_player_html_BasePlayerListener
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var string
     */
    private $_name;

    public function __construct($name,
                                tubepress_platform_api_log_LoggerInterface $logger,
                                tubepress_app_api_options_ContextInterface $context)
    {
        $this->_logger  = $logger;
        $this->_context = $context;
        $this->_name    = $name;
    }

    public function onPreRenderTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$this->_name = $this->_context->get(tubepress_app_api_options_Names::PLAYER_LOCATION)) {

            return;
        }

        //render player html
    }
}