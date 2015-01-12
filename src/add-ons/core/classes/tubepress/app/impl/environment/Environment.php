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
class tubepress_app_impl_environment_Environment implements tubepress_app_api_environment_EnvironmentInterface
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
        $this->_properties   = new tubepress_platform_impl_collection_Map();

        $this->_properties->put(self::$_PROPERTY_VERSION, tubepress_platform_api_version_Version::parse('9.9.9'));
        $this->_properties->put(self::$_PROPERTY_IS_PRO, false);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface The base TubePress URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getBaseUrl()
    {
        if (!$this->_properties->containsKey(self::$_PROPERTY_URL_BASE)) {

            return null;
        }

        return $this->_properties->get(self::$_PROPERTY_URL_BASE);
    }

    /**
     * Set the TubePress base URL.
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

        $asUrl->removeSchemeAndAuthority();

        $asUrl->freeze();

        $this->_properties->put(self::$_PROPERTY_URL_BASE, $asUrl);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface The user content URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUserContentUrl()
    {
        if (!$this->_properties->containsKey(self::$_PROPERTY_URL_USERCONTENT)) {

            if (defined('TUBEPRESS_CONTENT_URL')) {

                $url = rtrim(TUBEPRESS_CONTENT_URL, DIRECTORY_SEPARATOR);

            } else {

                if ($this->_isWordPress()) {

                    $url = $this->_wpFunctionsInterface->content_url();

                } else {

                    if (!$this->_properties->containsKey(self::$_PROPERTY_URL_BASE)) {

                        return null;
                    }

                    $url = $this->_properties->get(self::$_PROPERTY_URL_BASE)->toString();
                }

                $url = rtrim($url, '/') . '/tubepress-content';
            }

            try {

                $url = $this->_toUrl($url);

            } catch (InvalidArgumentException $e) {

                return null;
            }

            $url->removeSchemeAndAuthority();

            $url->freeze();

            $this->_properties->put(self::$_PROPERTY_URL_USERCONTENT, $url);
        }

        return $this->_properties->get(self::$_PROPERTY_URL_USERCONTENT);
    }

    /**
     * Set the user content URL.
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

        $asUrl->freeze();

        $this->_properties->put(self::$_PROPERTY_URL_USERCONTENT, $asUrl);
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

            throw new InvalidArgumentException('Invalid argument to tubepress_app_impl_environment_Environment::setWpFunctionsInterface');
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

        return $url;
    }

    private function _isWordPress()
    {
        return defined('WPLANG') && defined('ABSPATH');
    }
}