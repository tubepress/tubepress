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

class tubepress_wordpress_impl_actions_Ajax
{
    /**
     * @var tubepress_core_http_api_AjaxCommandInterface
     */
    private $_ajaxHandler;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParameters;

    public function __construct(tubepress_core_http_api_AjaxCommandInterface       $ajaxHandler,
                                tubepress_core_http_api_RequestParametersInterface $requestParams )
    {
        $this->_ajaxHandler      = $ajaxHandler;
        $this->_requestParameters = $requestParams;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function action(tubepress_core_event_api_EventInterface $event)
    {
        $this->_ajaxHandler->handle();
        exit;
    }

    public function onReadActionFromExternalInput(tubepress_core_event_api_EventInterface $event)
    {
        if (!$this->_requestParameters->hasParam('tubepress_wp_action')) {

            return;
        }

        $action = $this->_requestParameters->getParamValue('tubepress_wp_action');
        $event->setSubject($action);
    }
}