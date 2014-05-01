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

class tubepress_addons_wordpress_impl_Callback
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var bool
     */
    private $_baseUrlAlreadySet = false;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment, tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_environment     = $environment;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onFilter($filterName, array $args)
    {
        $this->_setBaseUrl();

        $subject         = $args[0];
        $args            = count($args) > 1 ? array_slice($args, 1) : array();
        $event           = new tubepress_spi_event_EventBase(

            $subject,
            array('args' => $args)
        );

        $this->_eventDispatcher->dispatch("tubepress.wordpress.filter.$filterName", $event);

        return $event->getSubject();
    }

    public function onAction($actionName, array $args)
    {
        $this->_setBaseUrl();

        $event           = new tubepress_spi_event_EventBase($args);

        $this->_eventDispatcher->dispatch("tubepress.wordpress.action.$actionName", $event);
    }

    public function onPluginActivation()
    {
        $this->_setBaseUrl();

        $service = tubepress_impl_patterns_sl_ServiceLocator::getService('wordpress.pluginActivator');

        $service->execute();
    }

    private function _setBaseUrl()
    {
        if ($this->_baseUrlAlreadySet) {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        /**
         * @var $wpFunctionWrapper tubepress_addons_wordpress_spi_WpFunctionsInterface
         */
        $wpFunctionWrapper =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if ($this->_isWordPressMuDomainMapped()) {

            $prefix = $this->_getScheme($wpFunctionWrapper) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $wpFunctionWrapper->content_url();
        }

        $this->_environment->setBaseUrl($prefix . "/plugins/$baseName");

        $this->_baseUrlAlreadySet = true;
    }

    private function _getScheme(tubepress_addons_wordpress_spi_WpFunctionsInterface $wpFunctionWrapper)
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