<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_api_options_ui_FormHandler',
    'org_tubepress_api_options_StorageManager'
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
        $fs           = $iocContainer->get(org_tubepress_api_filesystem_Explorer::_);
        $baseName     = $fs->getTubePressInstallationDirectoryBaseName();

        wp_register_style('jquery-ui-flick', plugins_url("$baseName/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.8.16.custom.css", $baseName));
        wp_register_style('tubepress-options-page', plugins_url("$baseName/sys/ui/static/css/wordpress-options-page.css", $baseName));
        wp_register_style('jquery-ui-multiselect-widget', plugins_url("$baseName/sys/ui/static/css/jquery-ui-multiselect-widget/jquery.multiselect.css", $baseName));

        wp_enqueue_style('jquery-ui-flick');
        wp_enqueue_style('tubepress-options-page');
        wp_enqueue_style('jquery-ui-multiselect-widget');

        wp_register_script('jscolor-tubepress', plugins_url("$baseName/sys/ui/static/js/jscolor/jscolor.js", $baseName));
        wp_register_script('jquery-ui-tubepress', plugins_url("$baseName/sys/ui/static/js/jquery-ui/jquery-ui-1.8.16.custom.min.js", $baseName));
        wp_register_script('jquery-ui-multiselect-widget', plugins_url("$baseName/sys/ui/static/js/jquery-ui-multiselect-widget/jquery.multiselect.min.js", $baseName));

        wp_enqueue_script('jquery-ui-tubepress');
        wp_enqueue_script('jquery-ui-multiselect-widget');
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
        $wpsm         = $iocContainer->get(org_tubepress_api_options_StorageManager::_);

        /* initialize our options in case we need to */
        $wpsm->init();

        /* get the form handler */
        $optionsForm = $iocContainer->get(org_tubepress_api_options_ui_FormHandler::_);
        $hrps        = $iocContainer->get(org_tubepress_api_http_HttpRequestParameterService::_);

        /* are we updating? */
        if ($hrps->hasParam('tubepress_save')) {

        	self::_verifyNonce();

            try {

                $result = $optionsForm->onSubmit();

                if ($result === null) {

                    echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';

                } else {

                    self::_error($result);
                }

            } catch (Exception $error) {

                self::_error($error->getMessage());
            }
        }

        print $optionsForm->getHtml();
    }

    private static function _verifyNonce() {

    	check_admin_referer('tubepress-save', 'tubepress-nonce');
    }

    private static function _error($message)
    {
        if (is_array($message)) {

            $message = implode($message, '<br />');
        }

        echo '<div id="message" class="error fade"><p><strong>' . $message . '</strong></p></div>';
    }
}

