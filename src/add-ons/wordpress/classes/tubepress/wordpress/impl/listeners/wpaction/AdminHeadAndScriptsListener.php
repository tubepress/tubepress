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

class tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_options_ui_FormInterface
     */
    private $_form;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_urlCache;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions        $wpFunctions,
                                tubepress_api_options_ui_FormInterface         $form,
                                tubepress_api_util_StringUtilsInterface        $stringUtils,
                                tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_form        = $form;
        $this->_stringUtils = $stringUtils;
        $this->_environment = $environment;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_head(tubepress_api_event_EventInterface $event)
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">';
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_enqueue_scripts(tubepress_api_event_EventInterface $eventInterface)
    {
        $args = $eventInterface->getSubject();
        $hook = $args[0];

        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $callback   = array($this, '__callbackConvertToWpUrlString');
        $cssUrls    = $this->_form->getUrlsCSS();
        $jsUrls     = $this->_form->getUrlsJS();
        $cssStrings = array_map($callback, $cssUrls);
        $jsStrings  = array_map($callback, $jsUrls);

        for ($x = 0; $x < count($cssStrings); $x++) {

            $this->_wpFunctions->wp_register_style('tubepress-' . $x, $cssStrings[$x]);
            $this->_wpFunctions->wp_enqueue_style('tubepress-' . $x);
        }

        for ($x = 0; $x < count($jsStrings); $x++) {

            $this->_wpFunctions->wp_register_script('tubepress-' . $x, $jsStrings[$x]);
            $this->_wpFunctions->wp_enqueue_script('tubepress-' . $x, false, array(), false, false);
        }
    }

    public function onAction_admin_print_scripts(tubepress_api_event_EventInterface $event)
    {
        $version = $this->_wpFunctions->wp_version();

        if (floatval($version) >= 3.6) {

            return;
        }

        $wpScripts = $this->_wpFunctions->wp_scripts();

        $wpScripts->remove('jquery');
        $wpScripts->remove('jquery-core');
        $wpScripts->remove('jquery-migrate');

        $wpScripts->add('jquery', false, array('jquery-core', 'jquery-migrate'), '1.11.3');
        $wpScripts->add('jquery-core', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js', array(), '1.11.3');
        $wpScripts->add('jquery-migrate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js', array(), '1.2.1');
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
