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

class tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
{
    private static $_NONCE_QUERY_PARAM_NAME = 'tubePressWpNonce';
    private static $_NONCE_ACTION           = 'tubePressDismissNag';

    private static $_DISMISS_NAG_QUERY_PARAM_NAME = 'dismissTubePressCacheNag';

    private static $_TRANSIENT_FORMAT = 'user_%d_dismiss_tubepress_nag';
    private static $_TRANSIENT_VALUE  = 'dismiss';

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var bool
     */
    private $_ignoreExceptions = true;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_app_api_options_ui_FormInterface
     */
    private $_form;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions           $wpFunctions,
                                tubepress_platform_api_url_UrlFactoryInterface    $urlFactory,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_app_api_options_ui_FormInterface        $form)
    {
        $this->_wpFunctions       = $wpFunctions;
        $this->_urlFactory        = $urlFactory;
        $this->_httpRequestParams = $requestParams;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_form              = $form;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_notices(tubepress_lib_api_event_EventInterface $event)
    {
        if (class_exists('TubePressServiceContainer', false)) {

            //all good in the hood
            return;
        }

        if (!$this->_wpFunctions->current_user_can('manage_options')) {

            //this user can't do anything about it.
            return;
        }

        try {

            if ($this->_userWantsToDismissNag($this->_wpFunctions)) {

                $this->_dismissNag($this->_wpFunctions);

            } else {

                $this->_nag($this->_wpFunctions);
            }

        } catch (Exception $e) {

            if (!$this->_ignoreExceptions) {

                throw $e;
            }
        }

    }

    private function _nag(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        if ($this->_nagIsDismissed($wpFunctions)) {

            return;
        }

        $nonce = $wpFunctions->wp_create_nonce(self::$_NONCE_ACTION);
        $url   = $this->_urlFactory->fromCurrent();
        $query = $url->getQuery();
        $urlToDocs = 'http://docs.tubepress.com/page/manual/wordpress/install-upgrade-uninstall.html#optimize-for-speed';

        $query->set(self::$_NONCE_QUERY_PARAM_NAME, $nonce);
        $query->set(self::$_DISMISS_NAG_QUERY_PARAM_NAME ,'true');

        $query = $url->getQuery();

        print <<<EOT
<div class="update-nag">
TubePress is not configured for optimal performance, and could be slowing down your site. <strong><a target="_blank" href="$urlToDocs">Fix it now</a></strong> or <a href="?$query">dismiss this message</a>.
</div>
EOT;
    }

    private function _nagIsDismissed(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $transientName  = $this->_getTransientName($wpFunctions);
        $transientValue = $wpFunctions->get_transient($transientName);

        return $transientValue === self::$_TRANSIENT_VALUE;
    }

    private function _userWantsToDismissNag(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        if (!$this->_httpRequestParams->hasParam(self::$_DISMISS_NAG_QUERY_PARAM_NAME)) {

            return false;
        }

        if ($this->_httpRequestParams->getParamValue(self::$_DISMISS_NAG_QUERY_PARAM_NAME) !== true) {

            return false;
        }

        if (!$this->_httpRequestParams->hasParam(self::$_NONCE_QUERY_PARAM_NAME)) {

            return false;
        }

        $nonceValue = $this->_httpRequestParams->getParamValue(self::$_NONCE_QUERY_PARAM_NAME);

        return $wpFunctions->wp_verify_nonce($nonceValue, self::$_NONCE_ACTION);
    }

    private function _dismissNag(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $transientName = $this->_getTransientName($wpFunctions);
        $wpFunctions->set_transient($transientName, self::$_TRANSIENT_VALUE, 86400);
    }

    private function _getTransientName(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $currentUser = $wpFunctions->wp_get_current_user();

        /** @noinspection PhpUndefinedFieldInspection */
        $id          = $currentUser->ID;

        return sprintf(self::$_TRANSIENT_FORMAT, $id);
    }

    /**
     * This is here strictly for testing :/
     */
    public function ___doNotIgnoreExceptions()
    {
        $this->_ignoreExceptions = false;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_menu(tubepress_lib_api_event_EventInterface $event)
    {
        $this->_wpFunctions->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, '__fireOptionsPageEvent'));
    }

    public function __fireOptionsPageEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED);
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_head(tubepress_lib_api_event_EventInterface $event)
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">';
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onFilter_row_meta(tubepress_lib_api_event_EventInterface $event)
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
            sprintf('<a href="http://docs.tubepress.com/">Documentation</a>'),
            sprintf('<a href="http://community.tubepress.com/">Support</a>'),
        ));

        $event->setSubject($toReturn);
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_enqueue_scripts(tubepress_lib_api_event_EventInterface $eventInterface)
    {
        $args = $eventInterface->getSubject();
        $hook = $args[0];

        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $cssUrls = $this->_form->getUrlsCSS();
        $jsUrls  = $this->_form->getUrlsJS();

        for ($x = 0; $x < count($cssUrls); $x++) {

            $cssUrl = $cssUrls[$x];

            $this->_wpFunctions->wp_register_style('tubepress-' . $x, $cssUrl->toString());
            $this->_wpFunctions->wp_enqueue_style('tubepress-' . $x);
        }

        for ($x = 0; $x < count($jsUrls); $x++) {

            $jsUrl = $jsUrls[$x];

            $this->_wpFunctions->wp_register_script('tubepress-' . $x, $jsUrl->toString());
            $this->_wpFunctions->wp_enqueue_script('tubepress-' . $x, false, array(), false, false);
        }
    }
}