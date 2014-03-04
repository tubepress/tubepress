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
     * @var bool Flag to prevent duplicate boot.
     */
    private static $_FLAG_TUBEPRESS_BOOTED = false;

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
        load_plugin_textdomain('tubepress', false, $this->_baseName . '/src/main/resources/i18n');
    }

    private function _addFilters()
    {
        $filterCallback = array($this, '__handlerFilter');

        add_filter('the_content', $filterCallback);
        add_filter($this->_calculateMetaRowsFilterPoint(), $filterCallback, 10, 2);
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

            add_action($interestingAction, $actionCallback);
        }
    }

    private function _addActivationHooks()
    {
        register_activation_hook($this->_baseName . '/tubepress.php', array($this, '__handlerActivationHook'));
    }

    public function __handlerFilter()
    {
        try {

            $this->_bootTubePress();

            return tubepress_addons_wordpress_impl_Callback::onFilter(current_filter(), func_get_args());

        } catch (Exception $e) {

            return func_get_args(0);
        }
    }

    public function __handlerAction()
    {
        try {

            $this->_bootTubePress();

            tubepress_addons_wordpress_impl_Callback::onAction(current_filter(), func_get_args());

        } catch (Exception $e) {

            //fail silently so we don't take down the whole site
        }
    }

    public function __handlerActivationHook()
    {
        try {

            $this->_bootTubePress();

            tubepress_addons_wordpress_impl_Callback::onPluginActivation(func_get_args());

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

    private function _bootTubePress()
    {
        if (self::$_FLAG_TUBEPRESS_BOOTED) {

            return;
        }

        if (!defined('TUBEPRESS_ROOT')) {

            require 'src/main/php/scripts/boot.php';
        }

        self::$_FLAG_TUBEPRESS_BOOTED = true;
    }
}

$apiIntegrator = new tubepress_impl_addons_wordpress_ApiIntegrator();
$apiIntegrator->load();