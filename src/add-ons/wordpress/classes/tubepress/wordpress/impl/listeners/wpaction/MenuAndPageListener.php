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

class tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_http_oauth2_impl_popup_AuthorizationInitiator
     */
    private $_oauth2AuthorizationInitiator;

    /**
     * @var tubepress_http_oauth2_impl_popup_RedirectionCallback
     */
    private $_oauth2Callback;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions                 $wpFunctions,
                                tubepress_api_event_EventDispatcherInterface            $eventDispatcher,
                                tubepress_http_oauth2_impl_popup_AuthorizationInitiator $oauth2Initiator,
                                tubepress_http_oauth2_impl_popup_RedirectionCallback    $oauth2Callback)
    {
        $this->_wpFunctions                  = $wpFunctions;
        $this->_eventDispatcher              = $eventDispatcher;
        $this->_oauth2AuthorizationInitiator = $oauth2Initiator;
        $this->_oauth2Callback               = $oauth2Callback;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_menu(tubepress_api_event_EventInterface $event)
    {
        $this->_wpFunctions->add_options_page(
            'TubePress Options',
            'TubePress',
            'manage_options',
            'tubepress',
            array($this, '__fireOptionsPageEvent')
        );

        $this->_wpFunctions->add_submenu_page(
            null, '', '', 'manage_options',
            'tubepress_oauth2_start',
            array($this, '__noop')
        );

        $this->_wpFunctions->add_submenu_page(
            null, '', '', 'manage_options',
            'tubepress_oauth2',
            array($this, '__noop')
        );
    }

    public function __noop()
    {
        //this is needed by the onAction_admin_menu()
    }

    public function __fireOptionsPageEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED);
    }

    public function onAction_load_admin_page_tubepress_oauth2_start(tubepress_api_event_EventInterface $event)
    {
        $this->_oauth2AuthorizationInitiator->initiate();
        exit;
    }

    public function onAction_load_admin_page_tubepress_oauth2(tubepress_api_event_EventInterface $event)
    {
        $this->_oauth2Callback->initiate();
        exit;
    }
}
