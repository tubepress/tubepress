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
 *
 */
class tubepress_app_impl_html_CssAndJsGenerationHelper
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_app_impl_theme_CurrentThemeService
     */
    private $_currentThemeService;

    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_cache;

    /**
     * @var string
     */
    private $_eventNameUrlsCss;

    /**
     * @var string
     */
    private $_eventNameUrlsJs;

    /**
     * @var string
     */
    private $_templateNameCss;

    /**
     * @var string
     */
    private $_templateNameJs;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_platform_api_contrib_RegistryInterface   $themeRegistry,
                                tubepress_lib_api_template_TemplatingInterface     $templating,
                                tubepress_app_impl_theme_CurrentThemeService       $currentThemeService,
                                tubepress_app_api_environment_EnvironmentInterface $environment,
                                $eventNameUrlsCss,
                                $eventNameUrlsJs,
                                $templateNameCss,
                                $templateNameJs)
    {
        $this->_eventDispatcher     = $eventDispatcher;
        $this->_themeRegistry       = $themeRegistry;
        $this->_templating          = $templating;
        $this->_currentThemeService = $currentThemeService;
        $this->_environment         = $environment;
        $this->_eventNameUrlsCss    = $eventNameUrlsCss;
        $this->_eventNameUrlsJs     = $eventNameUrlsJs;
        $this->_templateNameCss     = $templateNameCss;
        $this->_templateNameJs      = $templateNameJs;

        $this->_cache = new tubepress_platform_impl_collection_Map();
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsCSS()
    {
        return $this->_getUrls('cached-urls-css', 'getUrlsCSS', $this->_eventNameUrlsCss);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsJS()
    {
        return $this->_getUrls('cached-urls-js', 'getUrlsJS', $this->_eventNameUrlsJs);
    }

    private function _getUrls($cacheKey, $themeGetter, $eventName)
    {
        if (!$this->_cache->containsKey($cacheKey)) {

            $currentTheme   = $this->_currentThemeService->getCurrentTheme();
            $themeScripts   = $this->_recursivelyGetFromTheme($currentTheme, $themeGetter);

            $urls = $this->_fireEventAndReturnSubject($eventName, $themeScripts);

            $this->_cache->put($cacheKey, $urls);
        }

        return $this->_cache->get($cacheKey);
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getCSS()
    {
        $cssUrls      = $this->getUrlsCSS();
        $currentTheme = $this->_currentThemeService->getCurrentTheme();
        $css          = $this->_recursivelyGetFromTheme($currentTheme, 'getInlineCSS');

        return $this->_templating->renderTemplate($this->_templateNameCss, array(

            'inlineCSS' => $css,
            'urls'      => $cssUrls
        ));
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getJS()
    {
        $jsUrls = $this->getUrlsJS();

        return $this->_templating->renderTemplate($this->_templateNameJs, array('urls' => $jsUrls));
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        if ($raw instanceof tubepress_lib_api_event_EventInterface) {

            $event = $raw;

        } else {

            $event = $this->_eventDispatcher->newEventInstance($raw);
        }

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }

    private function _recursivelyGetFromTheme(tubepress_app_api_theme_ThemeInterface $theme, $getter)
    {
        $toReturn = $theme->$getter(
            $this->_environment->getBaseUrl(),
            $this->_environment->getUserContentUrl()
        );
        $parentThemeName = $theme->getParentThemeName();

        if (!$parentThemeName) {

            return $toReturn;
        }

        $theme = $this->_themeRegistry->getInstanceByName($parentThemeName);

        if (!$theme) {

            return $toReturn;
        }

        $fromParent = $this->_recursivelyGetFromTheme($theme, $getter);

        if (is_array($fromParent)) {

            $toReturn = array_merge($fromParent, $toReturn);

        } else {

            $toReturn = $fromParent . $toReturn;
        }

        return $toReturn;
    }
}