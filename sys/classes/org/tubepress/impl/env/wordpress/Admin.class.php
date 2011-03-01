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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_ioc_FreeWordPressPluginIocService',
    'org_tubepress_ioc_ProInWordPressIocService',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_options_FormHandler',
    'org_tubepress_api_filesystem_Explorer'));

class org_tubepress_impl_env_wordpress_Admin
{
    /**
     * Hook for WordPress init.
     *
     * @return void
     */
    public static function initAction()
    {
        if (!is_admin()) {
            return;
        }
        
        global $tubepress_base_url;
        
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs           = $iocContainer->get('org_tubepress_api_filesystem_Explorer');
        $dirName      = basename($fs->getTubePressBaseInstallationPath());
        
        wp_register_style('jquery-ui-flick', "$tubepress_base_url/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.7.2.custom.css");
        wp_register_script('jscolor-tubepress', "$tubepress_base_url/sys/ui/static/js/jscolor/jscolor.js");
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
        add_options_page('TubePress Options', 'TubePress', 'manage_options', __FILE__, array('org_tubepress_impl_env_wordpress_Admin', 'conditionalExecuteOptionsPage'));
    }

    /**
     * Registers the TubePress options page
     *
     * @return void
     */
    public static function conditionalExecuteOptionsPage()
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

