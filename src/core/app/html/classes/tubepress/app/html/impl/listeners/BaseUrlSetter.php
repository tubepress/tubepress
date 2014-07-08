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
class tubepress_app_html_impl_listeners_BaseUrlSetter
{
    /**
     * @var tubepress_app_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_lib_util_api_UrlUtilsInterface
     */
    private $_urlUtils;

    public function __construct(tubepress_app_environment_api_EnvironmentInterface $environment,
                                tubepress_lib_util_api_UrlUtilsInterface           $urlUtils)
    {
        $this->_environment = $environment;
        $this->_urlUtils    = $urlUtils;
    }

    public function onGlobalJsConfig(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $baseUrl        = $this->_environment->getBaseUrl();
        $userContentUrl = $this->_environment->getUserContentUrl();
        $baseUrl        = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($baseUrl);
        $userContentUrl = $this->_urlUtils->getAsStringWithoutSchemeAndAuthority($userContentUrl);

        $config['urls']['base'] = $baseUrl;
        $config['urls']['usr']  = $userContentUrl;

        $event->setSubject($config);
    }
}