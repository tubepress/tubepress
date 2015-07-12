<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Detects TubePress's environment
 */
class tubepress_environment_impl_Environment implements tubepress_app_api_environment_EnvironmentInterface
{
    /**
     * tubepress_platform_api_url_UrlInterface The base URL
     */
    private static $_PROPERTY_URL_BASE = 'urlBase';

    /**
     * tubepress_platform_api_url_UrlInterface The user content URL
     */
    private static $_PROPERTY_URL_USERCONTENT = 'urlUserContent';

    /**
     * tubepress_platform_api_url_UrlInterface The Ajax endpoint URL
     */
    private static $_PROPERTY_URL_AJAX = 'urlAjax';

    /**
     * 
     */
    private static $_PROPERTY_VERSION = 'version';

    /**
     * 
     */
    private static $_PROPERTY_IS_PRO = 'isPro';

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctionsInterface;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_platform_api_url_UrlFactoryInterface    $urlFactory,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings)
    {
        $this->_urlFactory   = $urlFactory;
        $this->_bootSettings = $bootSettings;
        $this->_properties   = new tubepress_internal_collection_Map();

        $this->_properties->put(self::$_PROPERTY_VERSION, tubepress_platform_api_version_Version::parse('9.9.9'));
        $this->_properties->put(self::$_PROPERTY_IS_PRO, false);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface The base TubePress URL.
     *
     * @throws RuntimeException If the base URL was not set or cannot be determined.
     *
     * @api
     * @since 4.0.0
     */
    public function getBaseUrl()
    {
        if (!$this->_properties->containsKey(self::$_PROPERTY_URL_BASE)) {

            /**
             * See if it was defined in boot settings.
             */
            $fromBootSettings = $this->_bootSettings->getUrlBase();

            if ($fromBootSettings) {

                $this->_properties->put(self::$_PROPERTY_URL_BASE, $fromBootSettings);

                return $fromBootSettings;
            }

            if (!$this->_isWordPress()) {

                throw new RuntimeException('Please specify TubePress base URL in tubepress-content/config/settings.php');
            }

            $baseName = basename(TUBEPRESS_ROOT);

            $prefix = $this->_getWpContentUrl();

            $url = rtrim($prefix, '/') . "/plugins/$baseName";
            $url = $this->_toUrl($url);

            $this->_properties->put(self::$_PROPERTY_URL_BASE, $url);
        }

        return $this->_properties->get(self::$_PROPERTY_URL_BASE);
    }

    /**
     * Set the TubePress base URL.
     *
     * @deprecated Use settings.php instead.
     *
     * @param string|tubepress_platform_api_url_UrlInterface $url The new base URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setBaseUrl($url)
    {
        $asUrl = $this->_toUrl($url);

        $this->_properties->put(self::$_PROPERTY_URL_BASE, $asUrl);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface The user content URL.
     *
     * @throws RuntimeException If the user content URL was not set or cannot be determined.
     *
     * @api
     * @since 4.0.0
     */
    public function getUserContentUrl()
    {
        if (!$this->_properties->containsKey(self::$_PROPERTY_URL_USERCONTENT)) {

            $fromBootSettings = $this->_bootSettings->getUrlUserContent();

            if ($fromBootSettings) {

                $this->_properties->put(self::$_PROPERTY_URL_USERCONTENT, $fromBootSettings);

                return $fromBootSettings;
            }

            if ($this->_isWordPress()) {

                $url = $this->_getWpContentUrl();

            } else {

                $url = $this->getBaseUrl()->toString();
            }

            $url = rtrim($url, '/') . '/tubepress-content';
            $url = $this->_toUrl($url);

            $this->_properties->put(self::$_PROPERTY_URL_USERCONTENT, $url);
        }

        return $this->_properties->get(self::$_PROPERTY_URL_USERCONTENT);
    }

    /**
     * Set the user content URL.
     *
     * @deprecated Use settings.php instead.
     *
     * @param string|tubepress_platform_api_url_UrlInterface $url The user content URL.
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setUserContentUrl($url)
    {
        $asUrl = $this->_toUrl($url);

        $this->_properties->put(self::$_PROPERTY_URL_USERCONTENT, $asUrl);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface The Ajax endpoint URL.
     *
     * @api
     * @since 4.0.9
     */
    public function getAjaxEndpointUrl()
    {
        if (!$this->_properties->containsKey(self::$_PROPERTY_URL_AJAX)) {

            /**
             * See if it was defined in boot settings.
             */
            $fromBootSettings = $this->_bootSettings->getUrlAjaxEndpoint();

            if ($fromBootSettings) {

                $this->_properties->put(self::$_PROPERTY_URL_AJAX, $fromBootSettings);

                return $fromBootSettings;
            }

            if ($this->_isWordPress()) {

                $url = $this->_wpFunctionsInterface->admin_url('admin-ajax.php');

            } else {

                $url = $this->getBaseUrl()->getClone()->setPath('/web/php/ajaxEndpoint.php');
            }

            $url = $this->_toUrl($url);

            $this->_properties->put(self::$_PROPERTY_URL_AJAX, $url);
        }

        return $this->_properties->get(self::$_PROPERTY_URL_AJAX);
    }

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isPro()
    {
        return $this->_properties->getAsBoolean(self::$_PROPERTY_IS_PRO);
    }

    /**
     * Get the current TubePress version.
     *
     * @return tubepress_platform_api_version_Version The current version.
     *
     * @api
     * @since 4.0.0
     */
    public function getVersion()
    {
        return $this->_properties->get(self::$_PROPERTY_VERSION);
    }

    /**
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @api
     * @since 4.0.0
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    public function setWpFunctionsInterface($wpFunctionsInterface)
    {
        if (!is_a($wpFunctionsInterface, 'tubepress_wordpress_impl_wp_WpFunctions')) {

            throw new InvalidArgumentException('Invalid argument to tubepress_environment_impl_Environment::setWpFunctionsInterface');
        }

        $this->_wpFunctionsInterface = $wpFunctionsInterface;
    }

    public function markAsPro()
    {
        $this->_properties->put(self::$_PROPERTY_IS_PRO, true);
    }

    private function _toUrl($url)
    {
        if (!($url instanceof tubepress_platform_api_url_UrlInterface)) {

            $url = $this->_urlFactory->fromString($url);
        }

        $url->freeze();

        return $url;
    }

    private function _isWordPress()
    {
        return defined('DB_USER') && defined('ABSPATH');
    }

    private function _getWpContentUrl()
    {
        $isWpMuDomainMapped = defined('DOMAIN_MAPPING') && constant('DOMAIN_MAPPING') && defined('COOKIE_DOMAIN');

        /** http://code.google.com/p/tubepress/issues/detail?id=495#c2 */
        if ($isWpMuDomainMapped) {

            $scheme = $this->_wpFunctionsInterface->is_ssl() ? 'https://' : 'http://';

            return $scheme . constant('COOKIE_DOMAIN') . '/wp-content';

        }

        return $this->_wpFunctionsInterface->content_url();
    }
}