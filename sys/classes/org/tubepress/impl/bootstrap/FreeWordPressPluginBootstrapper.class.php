<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_bootstrap_AbstractBootstrapper',
    'org_tubepress_impl_env_wordpress_Main',
    'org_tubepress_impl_env_wordpress_Admin',
    'org_tubepress_impl_env_wordpress_Widget'));

/**
 * Performs WordPress initialization.
 */
class org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapper extends org_tubepress_impl_bootstrap_AbstractBootstrapper
{
    /**
     * Perform boot procedure.
     *
     * @return void
     */
    protected function _doBoot()
    {
        global $tubepress_base_url;

        $baseName = $this->getBaseName();

        /* set the tubepress_base_url global */
        $tubepress_base_url = get_option('siteurl') . "/wp-content/plugins/$baseName";

        /* register the plugin's message bundles */
        load_plugin_textdomain('tubepress', false, "$baseName/i18n");

        add_filter('the_content', array('org_tubepress_impl_env_wordpress_Main',   'contentFilter'));
        add_action('wp_head', array('org_tubepress_impl_env_wordpress_Main',   'headAction'));
        add_action('init', array('org_tubepress_impl_env_wordpress_Main',   'initAction'));

        add_action('admin_menu', array('org_tubepress_impl_env_wordpress_Admin',  'menuAction'));
        add_action('admin_init', array('org_tubepress_impl_env_wordpress_Admin',  'initAction'));

        add_action('widgets_init', array('org_tubepress_impl_env_wordpress_Widget', 'initAction'));
    }

    /**
     * Get the name of this bootstrapper.
     *
     * @return void
     */
    protected function _getName()
    {
        return 'WordPress Bootstrapper';
    }
    
    protected function getBaseName()
    {
        /* have to consider that sometimes people may name the "tubepress" directory differently */
        $dirName  = realpath(dirname(__FILE__) . '/../../../../../../');
        
        return basename($dirName);
    }
}
