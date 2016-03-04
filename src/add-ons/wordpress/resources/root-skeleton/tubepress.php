<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.com
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and widgets. Upgrade to <a href="http://tubepress.com/pricing/">TubePress Pro</a> for more features!
Author: TubePress LLC
Version: 99.99.99
Author URI: http://tubepress.com

Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)

This file is part of TubePress (http://tubepress.com)

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
*/

class tubepress_wordpress_ApiIntegrator
{
    /**
     * @var string The base plugin directory.
     */
    private $_baseName;

    /**
     * Called when TubePress is loaded as a plugin. Adds the appropriate
     * filter and action callbacks.
     */
    public function load()
    {
        $this->_init();
        $this->_loadPluginTextDomain();
        $this->_addFilters();
        $this->_addActions();
        $this->_addActivationHooks();
        $this->_addShortcode();
        $this->_addUpdateChecker();
    }

    private function _init()
    {
        define('TUBEPRESS_ROOT', dirname(__FILE__));
        $this->_baseName = basename(TUBEPRESS_ROOT);
    }

    private function _loadPluginTextDomain()
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        load_plugin_textdomain('tubepress', false, $this->_baseName . '/src/translations');
    }

    private function _addFilters()
    {
        $filterCallback = array($this, '__onFilter');

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter($this->_calculateMetaRowsFilterPoint(), $filterCallback, 10, 2);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('upgrader_pre_install', $filterCallback, 10, 2);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('puc_request_info_query_args-tubepress', $filterCallback);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('puc_request_info_result-tubepress', $filterCallback);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('jetpack_photon_skip_for_url', $filterCallback, 10, 3);
    }

    private function _addActions()
    {
        $actionCallback     = array($this, '__onAction');
        $interestingActions = array(

            'admin_enqueue_scripts',
            'admin_head',
            'admin_menu',
            'admin_notices',
            'admin_print_scripts-settings_page_tubepress',
            'init',
            'in_plugin_update_message-' . $this->_baseName . '/tubepress.php',
            'load-admin_page_tubepress_oauth2',
            'load-admin_page_tubepress_oauth2_start',
            'widgets_init',
            'wp_ajax_nopriv_tubepress',
            'wp_ajax_tubepress',
            'wp_head',
        );

        foreach ($interestingActions as $interestingAction) {

            /** @noinspection PhpUndefinedFunctionInspection */
            add_action($interestingAction, $actionCallback);
        }
    }

    private function _addActivationHooks()
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        register_activation_hook($this->_baseName . '/tubepress.php', array($this, '__onActivation'));
    }

    private function _addShortcode()
    {
        $serviceContainer = $this->_getServiceContainer();

        /**
         * @var $persistence tubepress_api_options_PersistenceInterface
         */
        $persistence = $serviceContainer->get(tubepress_api_options_PersistenceInterface::_);
        $keyword     = $persistence->fetch(tubepress_api_options_Names::SHORTCODE_KEYWORD);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_shortcode($keyword, array($this, '__onShortcode'));
    }

    public function __onShortcode()
    {
        try {

            $callback = $this->_getCallback();
            $args     = func_get_args();

            return call_user_func_array(array($callback, 'onShortcode'), $args);

        } catch (Exception $e) {

            $this->_handleException($e);

            return func_get_arg(0);
        }
    }

    public function __onFilter()
    {
        try {

            $callback = $this->_getCallback();

            /** @noinspection PhpUndefinedFunctionInspection */
            $currentFilter = current_filter();

            $args = func_get_args();

            return $callback->onFilter($currentFilter, $args);

        } catch (Exception $e) {

            $this->_handleException($e);

            return func_get_arg(0);
        }
    }

    public function __onAction()
    {
        try {

            $callback = $this->_getCallback();

            /** @noinspection PhpUndefinedFunctionInspection */
            $currentFilter = current_filter();

            $args = func_get_args();

            $callback->onAction($currentFilter, $args);

        } catch (Exception $e) {

            $this->_handleException($e);
        }
    }

    public function __onActivation()
    {
        try {

            $callback = $this->_getCallback();

            $args = func_get_args();

            $callback->onPluginActivation($args);

        } catch (Exception $e) {

            $this->_handleException($e);
        }
    }

    private function _calculateMetaRowsFilterPoint()
    {
        global $wp_version;

        if (version_compare($wp_version, '2.8.alpha', '>')) {

            return 'plugin_row_meta';
        }

        return 'plugin_action_links';
    }

    /**
     * @return tubepress_wordpress_impl_Callback
     */
    private function _getCallback()
    {
        $serviceContainer = $this->_getServiceContainer();

        return $serviceContainer->get('tubepress_wordpress_impl_Callback');
    }

    private function _handleException(Exception $e)
    {
        $serviceContainer = $this->_getServiceContainer();

        $logger = $serviceContainer->get(tubepress_api_log_LoggerInterface::_);

        $logger->error($e->getMessage());
    }

    private function _addUpdateChecker()
    {
        /** @noinspection PhpIncludeInspection */
        require 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

        /** @noinspection PhpUndefinedFunctionInspection */
        PucFactory::buildUpdateChecker(

            'http://snippets.wp.tubepress.com/update.php',
            __FILE__,
            'tubepress'
        );
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface
     */
    private function _getServiceContainer()
    {
        return require 'src/php/scripts/boot.php';
    }
}

$apiIntegrator = new tubepress_wordpress_ApiIntegrator();
$apiIntegrator->load();