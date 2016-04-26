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
class tubepress_options_ui_impl_listeners_BootstrapIe8Listener
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var array
     */
    private $_serverVars;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_environment = $environment;
    }

    public function onAdminScripts(tubepress_api_event_EventInterface $event)
    {
        $urls = $event->getSubject();

        if (!$this->_isIE8OrLower() || !$this->_containsBootstrap($urls)) {

            return;
        }

        $baseUrl = $this->_environment->getBaseUrl()->getClone();

        $baseUrl->setPath('/web/admin-themes/admin-default/vendor/respond-1.4.2/respond.min.js');
        $baseUrl->freeze();
        array_unshift($urls, $baseUrl);

        $baseUrl = $baseUrl->getClone();
        $baseUrl->setPath('/web/admin-themes/admin-default/vendor/html5-shiv-3.7.0/html5shiv.js');
        $baseUrl->freeze();
        array_unshift($urls, $baseUrl);

        $event->setSubject($urls);
    }

    private function _containsBootstrap(array $urls)
    {
        /*
         * @var tubepress_api_url_UrlInterface[]
         */
        foreach ($urls as $url) {

            if (stripos($url->getPath(), 'bootstrap') !== false) {

                return true;
            }
        }

        return false;
    }

    private function _isIE8orLower()
    {
        $serverVars = $this->_getServerVars();

        if (!isset($serverVars['HTTP_USER_AGENT'])) {

            //no user agent for some reason
            return false;
        }

        $userAgent = $serverVars['HTTP_USER_AGENT'];

        if (stristr($userAgent, 'MSIE') === false) {

            //shortcut - MSIE is not in user-agent header
            return false;
        }

        if (!preg_match('/MSIE (.*?);/i', $userAgent, $m)) {

            //not IE
            return false;
        }

        if (!isset($m[1]) || !is_numeric($m[1])) {

            //couldn't parse version for some reason
            return false;
        }

        $version = (int) $m[1];

        return $version <= 8;
    }

    private function _getServerVars()
    {
        return isset($this->_serverVars) ? $this->_serverVars : $_SERVER;
    }

    /**
     * THIS IS HERE FOR TESTING ONLY.
     *
     * @param array $serverVars
     */
    public function __setServerVars(array $serverVars)
    {
        $this->_serverVars = $serverVars;
    }

}
