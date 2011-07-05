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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_env_wordpress_Admin',
    'org_tubepress_impl_env_wordpress_Main',
    'org_tubepress_impl_env_wordpress_Widget',
));

/**
 * Performs WordPress initialization.
 */
class org_tubepress_impl_plugin_listeners_WordPressBoot
{
    /**
     * Perform boot procedure.
     *
     * @return void
     */
    public function on_boot()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $ed  = $ioc->get('org_tubepress_api_environment_Detector');

        if (!$ed->isWordPress()) {
            return;
        }

        global $tubepress_base_url;

        $baseName = $this->getBaseName();

        /* set the tubepress_base_url global */
        $tubepress_base_url = get_option('siteurl') . "/wp-content/plugins/$baseName";

        /* register the plugin's message bundles */
        load_plugin_textdomain('tubepress', false, "$baseName/sys/i18n");

        add_filter('the_content', array('org_tubepress_impl_env_wordpress_Main', 'contentFilter'));
        add_action('wp_head',     array('org_tubepress_impl_env_wordpress_Main', 'headAction'));
        add_action('init',        array('org_tubepress_impl_env_wordpress_Main', 'initAction'));

        add_action('admin_menu',            array('org_tubepress_impl_env_wordpress_Admin', 'menuAction'));
        add_action('admin_enqueue_scripts', array('org_tubepress_impl_env_wordpress_Admin', 'initAction'));

        add_action('widgets_init', array('org_tubepress_impl_env_wordpress_Widget', 'initAction'));
    }

    protected function getBaseName()
    {
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fse  = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $base = $fse->getTubePressBaseInstallationPath();

        return basename($base);
    }
}
