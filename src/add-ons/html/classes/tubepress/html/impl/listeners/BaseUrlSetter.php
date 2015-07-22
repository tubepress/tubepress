<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_html_impl_listeners_BaseUrlSetter
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_environment = $environment;
    }

    public function onGlobalJsConfig(tubepress_api_event_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $baseUrl         = $this->_environment->getBaseUrl()->getClone();
        $userContentUrl  = $this->_environment->getUserContentUrl()->getClone();
        $ajaxEndpointUrl = $this->_environment->getAjaxEndpointUrl()->getClone();

        $baseUrl->removeSchemeAndAuthority();
        $userContentUrl->removeSchemeAndAuthority();
        $ajaxEndpointUrl->removeSchemeAndAuthority();

        $config['urls']['base'] = "$baseUrl";
        $config['urls']['usr']  = "$userContentUrl";
        $config['urls']['ajax'] = "$ajaxEndpointUrl";

        $event->setSubject($config);
    }
}