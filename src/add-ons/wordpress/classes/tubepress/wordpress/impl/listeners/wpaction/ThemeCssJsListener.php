<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_urlCache;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions        $wpFunctions,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_html_HtmlGeneratorInterface      $htmlGenerator,
                                tubepress_api_util_StringUtilsInterface        $stringUtils)
    {
        $this->_wpFunctions   = $wpFunctions;
        $this->_environment   = $environment;
        $this->_htmlGenerator = $htmlGenerator;
        $this->_stringUtils   = $stringUtils;
    }

    public function onAction_init(tubepress_api_event_EventInterface $event)
    {
        /* no need to queue any of this stuff up in the admin section or login page */
        if ($this->_wpFunctions->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);
        $ajaxUrl  = $this->_wpFunctions->plugins_url('web/js/wordpress-ajax.js', "$baseName/tubepress.php");
        $version  = $this->_environment->getVersion();

        $this->_wpFunctions->wp_register_script('tubepress_ajax', $ajaxUrl, array('tubepress'), "$version");

        $this->_wpFunctions->wp_enqueue_script('jquery', false, array(), false, false);
        $this->_wpFunctions->wp_enqueue_script('tubepress_ajax', false, array(), false, false);

        $this->_enqueueThemeResources($this->_wpFunctions, $version);
    }

    private function _enqueueThemeResources(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions,
                                            tubepress_api_version_Version $version)
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
                $deps   = array('tubepress');
            }

            $wpFunctions->wp_register_script($handle, $scriptsStrings[$x], $deps, "$version");
            $wpFunctions->wp_enqueue_script($handle, false, array(), false, false);
        }
    }

    public function __callbackConvertToWpUrlString(tubepress_api_url_UrlInterface $url)
    {
        if ($url->isAbsolute()) {

            return $url->toString();
        }

        if (!isset($this->_urlCache)) {

            $this->_urlCache = new tubepress_internal_collection_Map();

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
