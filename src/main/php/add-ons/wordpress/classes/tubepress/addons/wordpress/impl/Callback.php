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
     * @var bool
     */
    private static $_FLAG_BASE_URL_SET = false;

    public static function onFilter($filterName, array $args)
    {
        self::_setBaseUrl();

        $service = self::_fetchService("filter.$filterName");

        return $service->filter($args);
    }

    public static function onAction($actionName, array $args)
    {
        self::_setBaseUrl();

        $service = self::_fetchService("action.$actionName");

        $service->execute($args);
    }

    public static function onPluginActivation()
    {
        self::_setBaseUrl();

        $service = self::_fetchService("pluginActivator");

        $service->execute();
    }

    private static function _fetchService($serviceId)
    {
        return tubepress_impl_patterns_sl_ServiceLocator::getService("wordpress.$serviceId");
    }

    private static function _setBaseUrl()
    {
        if (self::$_FLAG_BASE_URL_SET) {

            return;
        }

        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $baseName            = basename(TUBEPRESS_ROOT);

        /**
         * @var $wpFunctionWrapper tubepress_addons_wordpress_spi_WpFunctionsInterface
         */
        $wpFunctionWrapper =
            tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if (self::_isWordPressMuDomainMapped()) {

            $prefix = self::_getScheme($wpFunctionWrapper) . constant('COOKIE_DOMAIN') . '/wp-content';

        } else {

            $prefix = $wpFunctionWrapper->content_url();
        }

        $environmentDetector->setBaseUrl($prefix . "/plugins/$baseName");

        self::$_FLAG_BASE_URL_SET = true;
    }

    private static function _getScheme(tubepress_addons_wordpress_spi_WpFunctionsInterface $wpFunctionWrapper)
    {
        if ($wpFunctionWrapper->is_ssl()) {

            return 'https://';
        }

        return 'http://';
    }

    private static function _isWordPressMuDomainMapped()
    {
        return defined('DOMAIN_MAPPING') && constant('DOMAIN_MAPPING') && defined('COOKIE_DOMAIN');
    }
}