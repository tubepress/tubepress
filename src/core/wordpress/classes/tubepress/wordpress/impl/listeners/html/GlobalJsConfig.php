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
class tubepress_wordpress_impl_listeners_html_GlobalJsConfig
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    public function onConfig(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        if (!isset($config['urls']['php'])) {

            $config['urls']['php'] = array();
        }

        if (!isset($config['urls']['php']['sys'])) {

            $config['urls']['php']['sys'] = array();
        }

        $config['urls']['php']['sys']['ajaxEndpoint'] = $this->_wpFunctions->admin_url('admin-ajax.php');

        $event->setSubject($config);
    }
}