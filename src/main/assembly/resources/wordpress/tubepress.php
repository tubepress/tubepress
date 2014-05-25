<?php
/**
Plugin Name: @TubePress@
Plugin URI: http://tubepress.com
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and/or sidebar. @description@
Author: TubePress LLC
Version: git-bleeding
Author URI: http://tubepress.com

Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)

This file is part of TubePress (http://tubepress.com)

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
*/

class tubepress_impl_addons_wordpress_ApiIntegrator
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
    }

    private function _init()
    {
        $this->_baseName = basename(dirname(__FILE__));
    }

    private function _loadPluginTextDomain()
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        load_plugin_textdomain('tubepress', false, $this->_baseName . '/src/main/resources/i18n');
    }

    private function _addFilters()
    {
        $filterCallback = array($this, '__handlerFilter');

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('the_content', $filterCallback);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter($this->_calculateMetaRowsFilterPoint(), $filterCallback, 10, 2);

        /** @noinspection PhpUndefinedFunctionInspection */
        add_filter('upgrader_pre_install', $filterCallback, 10, 2);
    }

    private function _addActions()
    {
        $actionCallback     = array($this, '__handlerAction');
        $interestingActions = array(

            'admin_enqueue_scripts',
            'admin_head',
            'admin_menu',
            'admin_notices',
            'init',
            'in_plugin_update_message-' . $this->_baseName . '/tubepress.php',
            'widgets_init',
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
        register_activation_hook($this->_baseName . '/tubepress.php', array($this, '__handlerActivationHook'));
    }

    public function __handlerFilter()
    {
        try {

            $callback = $this->_getCallback();

            /** @noinspection PhpUndefinedFunctionInspection */
            $currentFilter = current_filter();

            return $callback->onFilter($currentFilter, func_get_args());

        } catch (Exception $e) {

            return func_get_args(0);
        }
    }

    public function __handlerAction()
    {
        try {

            $callback = $this->_getCallback();

            /** @noinspection PhpUndefinedFunctionInspection */
            $currentFilter = current_filter();

            $callback->onAction($currentFilter, func_get_args());

        } catch (Exception $e) {

            //fail silently so we don't take down the whole site
        }
    }

    public function __handlerActivationHook()
    {
        try {

            $callback = $this->_getCallback();

            $callback->onPluginActivation(func_get_args());

        } catch (Exception $e) {

            //fail silently so we don't take down the whole site
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
        /** @noinspection PhpIncludeInspection */
        /**
         * @var $serviceContainer tubepress_api_ioc_ContainerInterface
         */
        $serviceContainer = require 'src/main/php/scripts/boot.php';

        return $serviceContainer->get('tubepress_wordpress_impl_Callback');
    }
}

$apiIntegrator = new tubepress_impl_addons_wordpress_ApiIntegrator();
$apiIntegrator->load();