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
 * Simple implementation of a theme.
 */
abstract class tubepress_app_impl_theme_AbstractTheme extends tubepress_platform_impl_contrib_AbstractContributable implements tubepress_app_api_theme_ThemeInterface
{
    /**
     * Optional attributes.
     */
    private static $_PROPERTY_SCRIPTS    = 'scripts';
    private static $_PROPERTY_STYLES     = 'styles';
    private static $_PROPERTY_PARENT     = 'parent';

    /**
     * @param tubepress_platform_api_url_UrlInterface $baseUrl        The TubePress base URL.
     * @param tubepress_platform_api_url_UrlInterface $userContentUrl The user content URL.
     *
     * @return tubepress_platform_api_url_UrlInterface[] An array, which may be empty but never null, of script URLs for
     *                                                   this theme.
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsJS(tubepress_platform_api_url_UrlInterface $baseUrl,
                               tubepress_platform_api_url_UrlInterface $userContentUrl)
    {
        return $this->getOptionalProperty(self::$_PROPERTY_SCRIPTS, array());
    }

    /**
     * @param tubepress_platform_api_url_UrlInterface $baseUrl        The TubePress base URL.
     * @param tubepress_platform_api_url_UrlInterface $userContentUrl The user content URL.
     *
     * @return tubepress_platform_api_url_UrlInterface[] An array, which may be empty but never null, of stylesheet URLs
     *                                                   for this theme.
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsCSS(tubepress_platform_api_url_UrlInterface $baseUrl,
                              tubepress_platform_api_url_UrlInterface $userContentUrl)
    {
        return $this->getOptionalProperty(self::$_PROPERTY_STYLES, array());
    }

    /**
     * @return string The name of this theme's parent. May be null.
     */
    public function getParentThemeName()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_PARENT, null);
    }

    public function setScripts(array $scripts)
    {
        $this->getProperties()->put(self::$_PROPERTY_SCRIPTS, $scripts);
    }

    public function setStyles(array $styles)
    {
        $this->getProperties()->put(self::$_PROPERTY_STYLES, $styles);
    }

    public function setParentThemeName($name)
    {
        $this->getProperties()->put(self::$_PROPERTY_PARENT, $name);
    }
}