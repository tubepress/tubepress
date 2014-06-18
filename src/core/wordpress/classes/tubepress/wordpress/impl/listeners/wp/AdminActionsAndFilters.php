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
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_currentUrlService;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions            $wpFunctions,
                                tubepress_core_url_api_UrlFactoryInterface         $currentUrlService,
                                tubepress_core_http_api_RequestParametersInterface $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_wpFunctions       = $wpFunctions;
        $this->_currentUrlService = $currentUrlService;
        $this->_httpRequestParams = $requestParams;
        $this->_eventDispatcher   = $eventDispatcher;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onAction_admin_notices(tubepress_core_event_api_EventInterface $event)
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
        $url   = $this->_currentUrlService->fromCurrent();
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
    public function onAction_admin_menu(tubepress_core_event_api_EventInterface $event)
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
    public function onAction_admin_head(tubepress_core_event_api_EventInterface $event)
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">';
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onFilter_row_meta(tubepress_core_event_api_EventInterface $event)
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
    public function onAction_admin_enqueue_scripts(tubepress_core_event_api_EventInterface $eventInterface)
    {
        $args = $eventInterface->getSubject();
        $hook = $args[0];

        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        foreach ($this->_getCssMap() as $cssName => $cssRelativePath) {

            $url = $this->_wpFunctions->plugins_url($baseName . $cssRelativePath, $baseName);

            $this->_wpFunctions->wp_register_style($cssName, $url);
            $this->_wpFunctions->wp_enqueue_style($cssName);
        }

        foreach ($this->_getJsMap() as $jsName => $jsRelativePath) {

            $url = $this->_wpFunctions->plugins_url($baseName . $jsRelativePath, $baseName);

            $this->_wpFunctions->wp_register_script($jsName, $url);
            $this->_wpFunctions->wp_enqueue_script($jsName, false, array(), false, false);
        }
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.1.1'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom.css',
            'bootstrap-theme'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect'   => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.4/css/bootstrap-multiselect.css',
            'blueimp-gallery-2.14.0'  => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/css/blueimp-gallery.min.css',
            'bootstrap-image-gallery' => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/css/bootstrap-image-gallery.css',
            'tubepress-options-gui'   => '/src/core/options-ui/web/css/options-page.css',
            'wordpress-options-gui'   => '/src/core/wordpress/web/options-gui/css/options-page.css',
            'spectrum'                => '/src/core/options-ui/web/vendor/spectrum-1.3.4/spectrum.css',
        );
    }

    private function _getJsMap()
    {
        $toReturn = array(

            'bootstrap-3.1.1' => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/js/bootstrap.min.js',
        );

        if ($this->_isIE8orLower()) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/core/options-ui/web/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.4.2'    => '/src/core/options-ui/web/vendor/respond-1.4.2/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.4/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/core/options-ui/web/vendor/spectrum-1.3.4/spectrum.js',
            'blueimp-gallery-2.14.0'        => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/js/blueimp-gallery.min.js',
            'bootstrap-image-gallery'       => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/js/bootstrap-image-gallery.js',
            'bootstrap-field-error-handler' => '/src/core/options-ui/web/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/core/options-ui/web/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/core/options-ui/web/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/core/options-ui/web/js/bootstrap-multiselect-initializer.js',
            'theme-field-handler'           => '/src/core/options-ui/web/js/theme-field-handler.js',
            'theme-reminder'                => '/src/core/wordpress/web/options-gui/js/theme-reminder.js',
            'iframe-loader'                 => '/src/core/wordpress/web/options-gui/js/iframe-loader.js',
        ));

        return $toReturn;
    }

    private function _isIE8orLower()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {

            //no user agent for some reason
            return false;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (stristr($userAgent, 'MSIE') === false) {

            //shortcut - MSIE is not in user-agent header
            return false;
        }

        if (!preg_match('/MSIE (.*?);/i', $userAgent, $m)) {

            //not IE
            return false;
        }

        if (!isset($m[1]) || !is_numeric($m[1])) {

            //couldn't parse version for some reason
            return false;
        }

        $version = (int) $m[1];

        return $version <= 8;
    }
}
