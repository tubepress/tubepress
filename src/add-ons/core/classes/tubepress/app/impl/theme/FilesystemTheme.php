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
 * Simple implementation of a theme.
 */
class tubepress_app_impl_theme_FilesystemTheme extends tubepress_app_impl_theme_AbstractTheme
{
    private static $_PROPERTY_TEMPLATE_NAMES_TO_PATHS = 'templateNamesToAbsPaths';
    private static $_PROPERTY_INLINE_CSS              = 'inlineCSS';
    private static $_PROPERTY_MANIFEST_PATH           = 'manifestPath';
    private static $_PROPERTY_IS_SYSTEM               = 'isSystem';

    /**
     * @return string Inline CSS that should be added to the <head> when this theme is active.
     *
     * @api
     * @since 4.0.0
     */
    public function getInlineCSS()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_INLINE_CSS, null);
    }

    /**
     * @param string $name The name of the template.
     *
     * @return string The template source.
     */
    public function getTemplateSource($name)
    {
        $map = $this->getProperties()->get(self::$_PROPERTY_TEMPLATE_NAMES_TO_PATHS);

        return file_get_contents($map[$name]);
    }

    /**
     * @param string $name A template name
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool True if the template has not since been modified, false otherwise.
     *
     * @throws InvalidArgumentException If
     */
    public function isTemplateSourceFresh($name, $time)
    {
        $path = $this->getTemplateCacheKey($name);

        return filemtime($path) < $time;
    }

    /**
     * @param string $name The template name.
     *
     * @return string The globally unique cache key for this template.
     */
    public function getTemplateCacheKey($name)
    {
        $map = $this->getProperties()->get(self::$_PROPERTY_TEMPLATE_NAMES_TO_PATHS);

        return $map[$name];
    }

    /**
     * @param string $name The name of the template.
     *
     * @return bool True if this theme contains source for the given template, false otherwise.
     */
    public function hasTemplateSource($name)
    {
        $map = $this->getProperties()->get(self::$_PROPERTY_TEMPLATE_NAMES_TO_PATHS);

        return isset($map[$name]);
    }

    public function setInlineCss($css)
    {
        $this->getProperties()->set(self::$_PROPERTY_INLINE_CSS, $css);
    }

    public function setTemplateNamesToAbsPathsMap(array $map)
    {
        $this->getProperties()->set(self::$_PROPERTY_TEMPLATE_NAMES_TO_PATHS, $map);
    }

    public function setManifestPath($path)
    {
        $this->getProperties()->set(self::$_PROPERTY_MANIFEST_PATH, $path);

        $themeAbsPath = dirname($path);
        $pathElements = array(TUBEPRESS_ROOT, 'web', 'themes');
        $needle       = implode(DIRECTORY_SEPARATOR, $pathElements);
        $isSystem     = strpos($themeAbsPath, $needle) !== false;

        $this->getProperties()->set(self::$_PROPERTY_IS_SYSTEM, $isSystem);
    }

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
        return $this->_getStylesOrScripts($baseUrl, $userContentUrl, 'getUrlsJS');
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
        return $this->_getStylesOrScripts($baseUrl, $userContentUrl, 'getUrlsCSS');
    }

    private function _getStylesOrScripts(tubepress_platform_api_url_UrlInterface $baseUrl,
                                         tubepress_platform_api_url_UrlInterface $userContentUrl,
                                         $getter)
    {
        $toReturn = parent::$getter($baseUrl, $userContentUrl);

        for ($x = 0; $x < count($toReturn); $x++) {

            $url = $toReturn[$x];

            if ($url->isAbsolute()) {

                continue;
            }

            if (strpos("$url", '/') === 0) {

                continue;
            }

            $toReturn[$x] = $this->_convertRelativeUrlToAbsolute($baseUrl, $userContentUrl, $url);
        }

        return $toReturn;
    }

    private function _convertRelativeUrlToAbsolute(tubepress_platform_api_url_UrlInterface $baseUrl,
                                                   tubepress_platform_api_url_UrlInterface $userContentUrl,
                                                   tubepress_platform_api_url_UrlInterface $candidate)
    {
        $toReturn = null;
        $manifestPath = $this->getProperties()->get(self::$_PROPERTY_MANIFEST_PATH);
        $themeBase    = basename(dirname($manifestPath));

        if ($this->getProperties()->getAsBoolean(self::$_PROPERTY_IS_SYSTEM)) {

            $toReturn = $baseUrl->getClone();
            $toReturn->addPath("/web/themes/$themeBase/$candidate");

        } else {

            $toReturn = $userContentUrl->getClone();
            $toReturn->addPath("/themes/$themeBase/$candidate");
        }

        return $toReturn;
    }
}