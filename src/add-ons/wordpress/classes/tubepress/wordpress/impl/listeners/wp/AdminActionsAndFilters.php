<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

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

    /**
     * @var tubepress_http_oauth2_impl_popup_AuthorizationInitiator
     */
    private $_oauth2AuthorizationInitiator;

    /**
     * @var tubepress_http_oauth2_impl_popup_RedirectionCallback
     */
    private $_oauth2Callback;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions                 $wpFunctions,
                                tubepress_api_url_UrlFactoryInterface                   $urlFactory,
                                tubepress_api_http_RequestParametersInterface           $requestParams,
                                tubepress_api_event_EventDispatcherInterface            $eventDispatcher,
                                tubepress_api_options_ui_FormInterface                  $form,
                                tubepress_api_util_StringUtilsInterface                 $stringUtils,
                                tubepress_api_environment_EnvironmentInterface          $environment,
                                tubepress_http_oauth2_impl_popup_AuthorizationInitiator $oauth2Initiator,
                                tubepress_http_oauth2_impl_popup_RedirectionCallback    $oauth2Callback,
                                tubepress_api_options_ContextInterface                  $context)
    {
        $this->_wpFunctions                  = $wpFunctions;
        $this->_urlFactory                   = $urlFactory;
        $this->_httpRequestParams            = $requestParams;
        $this->_eventDispatcher              = $eventDispatcher;
        $this->_form                         = $form;
        $this->_stringUtils                  = $stringUtils;
        $this->_environment                  = $environment;
        $this->_oauth2AuthorizationInitiator = $oauth2Initiator;
        $this->_oauth2Callback               = $oauth2Callback;
        $this->_context                      = $context;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_menu(tubepress_api_event_EventInterface $event)
    {
        $this->_wpFunctions->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, '__fireOptionsPageEvent'));

        $this->_wpFunctions->add_submenu_page(null, '', '', 'manage_options',
            'tubepress_oauth2_start', array($this, '__noop'));

        $this->_wpFunctions->add_submenu_page(null, '', '', 'manage_options',
            'tubepress_oauth2', array($this, '__noop'));
    }

    public function __noop()
    {
        //this is needed by the onAction_admin_menu()
    }

    public function __fireOptionsPageEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED);
    }

    public function onAction_load_admin_page_tubepress_oauth2_start(tubepress_api_event_EventInterface $event)
    {
        $this->_oauth2AuthorizationInitiator->initiate();
        exit;
    }

    public function onAction_load_admin_page_tubepress_oauth2(tubepress_api_event_EventInterface $event)
    {
        $this->_oauth2Callback->initiate();
        exit;
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
    public function onFilter_row_meta(tubepress_api_event_EventInterface $event)
    {
        $links = $event->getSubject();
        $args  = $event->getArgument('args');
        $file  = $args[0];

        $plugin = $this->_wpFunctions->plugin_basename(basename(TUBEPRESS_ROOT) . '/tubepress.php');

        if ($file != $plugin) {

            return;
        }

        $toReturn = array_merge($links, array(

            sprintf('<a href="options-general.php?page=tubepress.php">%s</a>', $this->_wpFunctions->__('Settings', 'tubepress')),
            sprintf('<a href="http://support.tubepress.com/">Support</a>'),
        ));

        $event->setSubject($toReturn);
    }

    public function onFilter_PucRequestInfoQueryArgsTubePress(tubepress_api_event_EventInterface $event)
    {
        $queryArgs = $event->getSubject();

        if ($this->_environment->isPro()) {

            $apiKey = $this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY);

            if ($apiKey) {

                $queryArgs['key'] = $apiKey;
                $queryArgs['pid'] = 2;
            }
        }

        $event->setSubject($queryArgs);
    }

    public function onFilter_PucRequestInfoResultTubePress(tubepress_api_event_EventInterface $event)
    {
        $pluginInfo = $event->getSubject();

        if ($pluginInfo && $this->_environment->isPro()) {

            $apiKey = $this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY);

            if (!$apiKey) {

                /**
                 * We don't want to downgrade Pro users that haven't entered an API key.
                 */
                $pluginInfo->download_url = null;
            }

            if (strpos($pluginInfo->download_url, 'free') !== false) {

                /**
                 * Extra assurance that we don't downgrade Pro users
                 */
                $pluginInfo->download_url = null;
            }

            if ($pluginInfo->download_url && $apiKey) {

                $url = $this->_urlFactory->fromString($pluginInfo->download_url);
                $url->getQuery()->set('key', $apiKey)->set('pid', 2);
                $pluginInfo->download_url = $url->toString();
            }
        }

        $event->setSubject($pluginInfo);
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

        $wpScripts->add('jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.11.3' );
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

    public function onAction_in_plugin_update_message(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY)) {

            printf('<br /><div class="inline notice notice-warning">Enable automatic updates by supplying your TubePress API Key. <a href="%s" target="_blank">Learn how</a>.</div>',
                'http://support.tubepress.com/customer/portal/articles/2278860-enable-automatic-plugin-updates'
            );
        }
    }
}