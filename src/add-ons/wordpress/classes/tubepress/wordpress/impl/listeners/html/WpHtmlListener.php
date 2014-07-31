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
class tubepress_wordpress_impl_listeners_html_WpHtmlListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions        $wpFunctions,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_urlFactory  = $urlFactory;
    }

    public function onCss(tubepress_lib_api_event_EventInterface $event)
    {
        $event->setSubject(array());
    }

    public function onJs(tubepress_lib_api_event_EventInterface $event)
    {
        $this->onCss($event);
    }

    public function onGlobalJsConfig(tubepress_lib_api_event_EventInterface $event)
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

        $adminAjaxUrl = $this->_wpFunctions->admin_url('admin-ajax.php');
        $adminAjaxUrl = $this->_urlFactory->fromString($adminAjaxUrl);

        $adminAjaxUrl->removeSchemeAndAuthority();

        $config['urls']['php']['sys']['ajaxEndpoint'] = "$adminAjaxUrl";

        $event->setSubject($config);
    }
}