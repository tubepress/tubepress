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

class tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_platform_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_app_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_lib_api_http_AjaxInterface
     */
    private $_ajaxHandler;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_lib_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_urlCache;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions            $wpFunctions,
                                tubepress_platform_api_util_StringUtilsInterface   $stringUtils,
                                tubepress_app_api_html_HtmlGeneratorInterface      $htmlGenerator,
                                tubepress_lib_api_http_AjaxInterface               $ajaxHandler,
                                tubepress_lib_api_http_RequestParametersInterface  $requestParams,
                                tubepress_lib_api_translation_TranslatorInterface  $translator,
                                tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_wpFunctions       = $wpFunctions;
        $this->_stringUtils       = $stringUtils;
        $this->_htmlGenerator     = $htmlGenerator;
        $this->_ajaxHandler       = $ajaxHandler;
        $this->_requestParameters = $requestParams;
        $this->_translator        = $translator;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_environment       = $environment;
    }

    public function onAction_widgets_init(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$event->hasArgument('unit-testing') && !class_exists('tubepress_wordpress_impl_wp_WpWidget')) {

            require TUBEPRESS_ROOT . '/src/add-ons/wordpress/classes/tubepress/wordpress/impl/wp/WpWidget.php';
        }

        $this->_wpFunctions->register_widget('tubepress_wordpress_impl_wp_WpWidget');

        /**
         * These next three lines are deprecated!
         */
        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $this->_translator->trans('Displays YouTube or Vimeo videos with TubePress. Limited to a single instance per site. Use the other TubePress widget instead!'));  //>(translatable)<
        $this->_wpFunctions->wp_register_sidebar_widget('tubepress', 'TubePress (legacy)', array($this, '__fireWidgetHtmlEvent'), $widgetOps);
        $this->_wpFunctions->wp_register_widget_control('tubepress', 'TubePress (legacy)', array($this, '__fireWidgetControlEvent'));
    }

    /**
     * @deprecated
     */
    public function __fireWidgetHtmlEvent($widgetOpts)
    {
        $event = $this->_eventDispatcher->newEventInstance($widgetOpts);

        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML, $event);
    }

    /**
     * @deprecated
     */
    public function __fireWidgetControlEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS);
    }

    public function onAction_init(tubepress_lib_api_event_EventInterface $event)
    {
        /* no need to queue any of this stuff up in the admin section or login page */
        if ($this->_wpFunctions->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);
        $ajaxUrl  = $this->_wpFunctions->plugins_url("web/js/wordpress-ajax.js", "$baseName/tubepress.php");
        $version  = $this->_environment->getVersion();

        $this->_wpFunctions->wp_register_script('tubepress_ajax', $ajaxUrl, array('tubepress'), "$version");

        $this->_wpFunctions->wp_enqueue_script('jquery', false, array(), false, false);
        $this->_wpFunctions->wp_enqueue_script('tubepress_ajax', false, array(), false, false);

        $this->_enqueueThemeResources($this->_wpFunctions, $version);
    }

    public function onAction_wp_head(tubepress_lib_api_event_EventInterface $event)
    {
        /* no need to print anything in the head of the admin section */
        if ($this->_wpFunctions->is_admin()) {

            return;
        }

        /* this inline JS helps initialize TubePress */
        print $this->_htmlGenerator->getCSS();
        print $this->_htmlGenerator->getJS();
    }

    public function onAction_ajax(tubepress_lib_api_event_EventInterface $event)
    {
        $this->_ajaxHandler->handle();
        exit;
    }

    private function _enqueueThemeResources(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions,
                                            tubepress_platform_api_version_Version $version)
    {
        $callback       = array($this, '__callbackConvertToWpUrlString');
        $stylesUrls     = $this->_htmlGenerator->getUrlsCSS();
        $scriptsUrls    = $this->_htmlGenerator->getUrlsJS();
        $stylesStrings  = array_map($callback, $stylesUrls);
        $scriptsStrings = array_map($callback, $scriptsUrls);
        $styleCount     = count($stylesStrings);
        $scriptCount    = count($scriptsStrings);

        for ($x = 0; $x < $styleCount; $x++) {

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_style($handle, $stylesStrings[$x], array(), "$version");
            $wpFunctions->wp_enqueue_style($handle);
        }

        for ($x = 0; $x < $scriptCount; $x++) {

            if ($this->_stringUtils->endsWith($scriptsStrings[$x], '/web/js/tubepress.js')) {

                $handle = 'tubepress';
                $deps   = array();

            } else {

                $handle = 'tubepress-theme-' . $x;
                $deps    = array('tubepress');
            }

            $wpFunctions->wp_register_script($handle, $scriptsStrings[$x], $deps, "$version");
            $wpFunctions->wp_enqueue_script($handle, false, array(), false, false);
        }
    }

    public function __callbackConvertToWpUrlString(tubepress_platform_api_url_UrlInterface $url)
    {
        if ($url->isAbsolute()) {

            return $url->toString();
        }

        if (!isset($this->_urlCache)) {

            $this->_urlCache = new tubepress_platform_impl_collection_Map();

            $this->_urlCache->put('url.base', rtrim($this->_environment->getBaseUrl()->toString(), '/'));
            $this->_urlCache->put('url.user', rtrim($this->_environment->getUserContentUrl()->toString(), '/'));
            $this->_urlCache->put('basename', basename(TUBEPRESS_ROOT));
        }

        $urlAsString      = $url->toString();
        $tubePressBaseUrl = $this->_urlCache->get('url.base');
        $userContentUrl   = $this->_urlCache->get('url.user');
        $baseName         = $this->_urlCache->get('basename');
        $isSystem         = false;

        if ($this->_stringUtils->startsWith($urlAsString, "$tubePressBaseUrl/web/")) {

            $isSystem = true;

        } else if (!$this->_stringUtils->startsWith($urlAsString, "$userContentUrl/")) {

            //this should never happen
            return $urlAsString;
        }

        if ($isSystem) {

            $path = str_replace($tubePressBaseUrl, '', $urlAsString);

            return $this->_wpFunctions->plugins_url($path, "$baseName/tubepress.php");
        }

        $path = str_replace($userContentUrl, '', $urlAsString);

        return $this->_wpFunctions->content_url('tubepress-content' . $path);
    }
}
