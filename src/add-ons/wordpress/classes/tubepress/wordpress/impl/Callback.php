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

class tubepress_wordpress_impl_Callback
{
    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var bool
     */
    private $_baseUrlAlreadySet = false;

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_wordpress_impl_wp_ActivationHook
     */
    private $_activationHook;

    public function __construct(tubepress_app_api_environment_EnvironmentInterface $environment,
                                tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_wordpress_impl_wp_WpFunctions            $wpFunctions,
                                tubepress_wordpress_impl_wp_ActivationHook         $activationHook)
    {
        $this->_environment     = $environment;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_wpFunctions     = $wpFunctions;
        $this->_activationHook  = $activationHook;
    }

    public function onFilter($filterName, array $args)
    {
        $this->_setBaseUrl();

        $subject = $args[0];
        $args    = count($args) > 1 ? array_slice($args, 1) : array();
        $event   = $this->_eventDispatcher->newEventInstance(

            $subject,
            array('args' => $args)
        );

        $this->_eventDispatcher->dispatch("tubepress.wordpress.filter.$filterName", $event);

        return $event->getSubject();
    }

    public function onAction($actionName, array $args)
    {
        $this->_setBaseUrl();

        $event = $this->_eventDispatcher->newEventInstance($args);

        $this->_eventDispatcher->dispatch("tubepress.wordpress.action.$actionName", $event);
    }

    public function onPluginActivation()
    {
        $this->_setBaseUrl();

        $this->_activationHook->execute();
    }

    private function _setBaseUrl()
    {
        if ($this->_baseUrlAlreadySet) {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);


        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if ($this->_isWordPressMuDomainMapped()) {

            $prefix = $this->_getScheme($this->_wpFunctions) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $this->_wpFunctions->content_url();
        }

        $this->_environment->setBaseUrl($prefix . "/plugins/$baseName");

        $this->_baseUrlAlreadySet = true;
    }

    private function _getScheme(tubepress_wordpress_impl_wp_WpFunctions $wpFunctionWrapper)
    {
        if ($wpFunctionWrapper->is_ssl()) {

            return 'https://';
        }

        return 'http://';
    }

    private function _isWordPressMuDomainMapped()
    {
        return defined('DOMAIN_MAPPING') && constant('DOMAIN_MAPPING') && defined('COOKIE_DOMAIN');
    }
}