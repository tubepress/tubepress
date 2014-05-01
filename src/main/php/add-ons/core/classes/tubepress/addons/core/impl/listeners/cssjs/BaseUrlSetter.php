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
class tubepress_addons_core_impl_listeners_cssjs_BaseUrlSetter
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_environment = $environment;
    }

    public function onJsConfig(tubepress_api_event_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $config['urls']['base'] = $this->_environment->getBaseUrl()->toString();
        $config['urls']['usr']  = $this->_environment->getUserContentUrl()->toString();

        $event->setSubject($config);
    }
}