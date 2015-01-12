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
 * TubePress theme.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_api_theme_ThemeInterface extends tubepress_platform_api_contrib_ContributableInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_api_theme_ThemeInterface';

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
    function getUrlsJS(tubepress_platform_api_url_UrlInterface $baseUrl,
                        tubepress_platform_api_url_UrlInterface $userContentUrl);

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
    function getUrlsCSS(tubepress_platform_api_url_UrlInterface $baseUrl,
                       tubepress_platform_api_url_UrlInterface $userContentUrl);

    /**
     * @return string Inline CSS that should be added to the <head> when this theme is active.
     *
     * @api
     * @since 4.0.0
     */
    function getInlineCSS();

    /**
     * @return string The name of this theme's parent. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getParentThemeName();

    /**
     * @param string $name The name of the template.
     *
     * @return bool True if this theme contains source for the given template, false otherwise.
     */
    function hasTemplateSource($name);

    /**
     * @param string $name The name of the template.
     *
     * @return string The template source.
     */
    function getTemplateSource($name);

    /**
     * @param string $name A template name
     * @param int    $time The last modification time of the cached template (timestamp)
     *
     * @return bool True if the template has not since been modified, false otherwise.
     */
    function isTemplateSourceFresh($name, $time);

    /**
     * @param string $name The template name.
     *
     * @return string The globally unique cache key for this template.
     */
    function getTemplateCacheKey($name);
}