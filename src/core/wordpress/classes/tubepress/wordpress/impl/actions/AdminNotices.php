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

class tubepress_wordpress_impl_actions_AdminNotices
{
    private static $_NONCE_QUERY_PARAM_NAME = 'tubePressWpNonce';
    private static $_NONCE_ACTION           = 'tubePressDismissNag';

    private static $_DISMISS_NAG_QUERY_PARAM_NAME = 'dismissTubePressCacheNag';

    private static $_TRANSIENT_FORMAT = 'user_%d_dismiss_tubepress_nag';
    private static $_TRANSIENT_VALUE  = 'dismiss';

    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
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

    public function __construct(tubepress_core_url_api_UrlFactoryInterface         $currentUrlService,
                                tubepress_wordpress_spi_WpFunctionsInterface       $wpFunctions,
                                tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_currentUrlService = $currentUrlService;
        $this->_wpFunctions       = $wpFunctions;
        $this->_httpRequestParams = $requestParams;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $event)
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

    private function _nag(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
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

    private function _nagIsDismissed(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $transientName  = $this->_getTransientName($wpFunctions);
        $transientValue = $wpFunctions->get_transient($transientName);

        return $transientValue === self::$_TRANSIENT_VALUE;
    }

    private function _userWantsToDismissNag(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
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

    private function _dismissNag(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $transientName = $this->_getTransientName($wpFunctions);
        $wpFunctions->set_transient($transientName, self::$_TRANSIENT_VALUE, 86400);
    }

    private function _getTransientName(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
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
}
