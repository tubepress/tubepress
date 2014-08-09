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
 *
 */
class tubepress_app_impl_html_HtmlGenerator implements tubepress_app_api_html_HtmlGeneratorInterface
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

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_platform_api_contrib_RegistryInterface   $themeRegistry,
                                tubepress_lib_api_template_TemplatingInterface     $templating,
                                tubepress_app_impl_theme_CurrentThemeService       $currentThemeService,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_eventDispatcher     = $eventDispatcher;
        $this->_themeRegistry       = $themeRegistry;
        $this->_templating          = $templating;
        $this->_currentThemeService = $currentThemeService;
        $this->_environment         = $environment;

        $this->_cache = new tubepress_platform_impl_collection_Map();
    }

    /**
     * Generates the HTML for the given shortcode.
     *
     * @return string The HTML, or the error message if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    public function getHtml()
    {
        try {

            $htmlProviderSelectionEvent = $this->_eventDispatcher->newEventInstance('');

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_GENERATION, $htmlProviderSelectionEvent);

            /**
             * @var $selected string
             */
            $html = $htmlProviderSelectionEvent->getSubject();

            if ($html === null) {

                throw new RuntimeException('Unable to generate HTML.');
            }

            return $html;

        } catch (Exception $e) {

            $event = $this->_eventDispatcher->newEventInstance($e);

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT, $event);

            $args = array('exception' => $e);
            $html = $this->_templating->renderTemplate('exception/static', $args);

            return $html;
        }
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsCSS()
    {
        if (!$this->_cache->containsKey('cached-urls-css')) {

            $currentTheme = $this->_currentThemeService->getCurrentTheme();
            $urls         = $this->_recursivelyGetFromTheme($currentTheme, 'getUrlsCSS');
            $urls         = $this->_fireEventAndReturnSubject(tubepress_app_api_event_Events::HTML_STYLESHEETS, $urls);

            $this->_cache->put('cached-urls-css', $urls);
        }

        return $this->_cache->get('cached-urls-css');
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsJS()
    {
        if (!$this->_cache->containsKey('cached-urls-js')) {

            $currentTheme   = $this->_currentThemeService->getCurrentTheme();
            $themeScripts   = $this->_recursivelyGetFromTheme($currentTheme, 'getUrlsJS');
            $tubepressJsUrl = $this->_environment->getBaseUrl()->getClone();

            $tubepressJsUrl->addPath('/web/js/tubepress.js');

            array_unshift($themeScripts, $tubepressJsUrl);

            $urls = $this->_fireEventAndReturnSubject(tubepress_app_api_event_Events::HTML_SCRIPTS, $themeScripts);

            $this->_cache->put('cached-urls-js', $urls);
        }

        return $this->_cache->get('cached-urls-js');
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

        return $this->_templating->renderTemplate('cssjs/styles', array(

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

        return $this->_templating->renderTemplate('cssjs/scripts', array('urls' => $jsUrls));
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