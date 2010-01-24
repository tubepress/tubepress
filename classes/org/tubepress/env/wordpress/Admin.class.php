<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Displays gorgeous YouTube galleries in your posts, pages, and/or sidebar. Upgrade to <a href="http://tubepress.org/features/">TubePress Pro</a> for more features!
Author: Eric D. Hough
Version: 1.8.8
Author URI: http://ehough.com

Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)

This file is part of TubePress (http://tubepress.org)

TubePress is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

TubePress is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
*/

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_ioc_DefaultIocService',
    'org_tubepress_ioc_ProInWordPressIocService',
    'org_tubepress_ioc_IocService'));

class org_tubepress_env_wordpress_Admin
{
    public static function headAction()
    {
        global $tubepress_base_url;
        $jsColorFile = "$tubepress_base_url/ui/options_page/js/jscolor/jscolor.js";
        echo "<script type=\"text/javascript\" src=\"$jsColorFile\"></script>";
    }

    public static function initAction()
    {
        $dirName = basename(realpath(dirname(__FILE__) . '/../../../../..'));
	wp_enqueue_style('jquery-ui-flick', WP_PLUGIN_URL . "/$dirName/ui/options_page/css/flick/jquery-ui-1.7.2.custom.css");
	wp_enqueue_script('jquery-ui-tabs');
    }

    public static function menuAction()
    {
        add_options_page('TubePress Options', 'TubePress', 'administrator', __FILE__, array('org_tubepress_env_wordpress_Admin', 'conditionalExecuteOptionsPage'));
    }

    public static function conditionalExecuteOptionsPage()
    {
        if (version_compare(PHP_VERSION, '5.0.2', '>=')) {
            org_tubepress_env_wordpress_Admin::_executeOptionsPage();
        } else {
        print <<<EOT
<div id="message" class="error fade">
    <p>
        <strong>
            This version of TubePress requires PHP 5.0.2 or higher. 
            Please <a href="http://php.net">upgrade your PHP installation</a> 
            or visit <a href="http://tubepress.org">tubepress.org</a> to obtain 
            a different version of the plugin.
        </strong>
    </p>
</div>
EOT
    ;
        }
    }

    private static function _executeOptionsPage()
    {
        /* grab the storage manager */
        if (class_exists('org_tubepress_ioc_ProInWordPressIocService')) {
            $iocContainer = new org_tubepress_ioc_ProInWordPressIocService();
        } else {
            $iocContainer = new org_tubepress_ioc_DefaultIocService();
        }
        $wpsm = $iocContainer->get(org_tubepress_ioc_IocService::STORAGE_MANAGER);
        
        /* initialize our options in case we need to */
        $wpsm->init();
        
        /* get the form handler */
        $optionsForm = $iocContainer->get(org_tubepress_ioc_IocService::OPTIONS_FORM_HANDLER);
        
        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            try {
                $optionsForm->collect($_POST);
                echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
            } catch (Exception $error) {
            echo '<div id="message" class="error fade"><p><strong>' . $error->getMessage() . '</strong></p></div>';
            }
        }
        $optionsForm->display();
    }
}

?>
