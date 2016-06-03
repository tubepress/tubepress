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

class tubepress_http_impl_listeners_UserAgentListener
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_environment = $environment;
    }

    public function onRequest(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var tubepress_api_http_message_RequestInterface
         */
        $request = $event->getSubject();

        $request->setHeader('User-Agent', $this->_getUserAgent());
    }

    private function _getUserAgent()
    {
        $toReturn = (string) 'tubepress/' . $this->_environment->getVersion();

        if (extension_loaded('curl')) {

            $curlVersion = curl_version();
            $toReturn .= ' curl/' . $curlVersion['version'];
        }

        $toReturn .= ' PHP/' . PHP_VERSION;

        return $toReturn;
    }
}
