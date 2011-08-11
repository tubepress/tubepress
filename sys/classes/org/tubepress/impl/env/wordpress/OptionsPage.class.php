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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_impl_ioc_FreeWordPressPluginIocService',
    'org_tubepress_impl_options_FormHandler',
));

class org_tubepress_impl_env_wordpress_OptionsPage
{
    /**
     * Hook for WordPress init.
     *
     * @return void
     */
    public static function initAction($hook)
    {
        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {
            return;
        }

        global $tubepress_base_url;

        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs           = $iocContainer->get('org_tubepress_api_filesystem_Explorer');
        $baseName     = $fs->getTubePressInstallationDirectoryBaseName();

        wp_register_style('jquery-ui-flick', plugins_url("$baseName/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.7.2.custom.css", $baseName));
        wp_register_script('jscolor-tubepress', plugins_url("$baseName/sys/ui/static/js/jscolor/jscolor.js", $baseName));
        wp_enqueue_style('jquery-ui-flick');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jscolor-tubepress');
    }

    /**
     * Hook for WordPress admin menu.
     *
     * @return void
     */
    public static function menuAction()
    {
        add_options_page('TubePress Options', 'TubePress', 'manage_options', 'tubepress', array('org_tubepress_impl_env_wordpress_OptionsPage', 'executeOptionsPage'));
    }

    /**
     * Registers the TubePress options page
     *
     * @return void
     */
    public static function executeOptionsPage()
    {
        /* grab the storage manager */
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $wpsm         = $iocContainer->get('org_tubepress_api_options_StorageManager');

        /* initialize our options in case we need to */
        $wpsm->init();

        /* get the form handler */
        $optionsForm = $iocContainer->get('org_tubepress_impl_options_FormHandler');

        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            try {
                $optionsForm->collect($_POST);
                echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
            } catch (Exception $error) {
                echo '<div id="message" class="error fade"><p><strong>' . $error->getMessage() . '</strong></p></div>';
            }
        }
        print $optionsForm->getHtml();
    }
}

