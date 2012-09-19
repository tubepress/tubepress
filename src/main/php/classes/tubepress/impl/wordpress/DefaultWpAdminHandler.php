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

class tubepress_impl_wordpress_DefaultWpAdminHandler implements tubepress_spi_wordpress_WpAdminHandler
{
    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param string $hook The WP hook.
     *
     * @return void
     */
    function registerStylesAndScripts($hook)
    {
        /* only run on TubePress settings page */
        if ($hook !== 'settings_page_tubepress') {

            return;
        }

        $fs                   = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();
        $wpFunctionWrapper    = tubepress_impl_wordpress_WordPressServiceLocator::getWordPressFunctionWrapper();
        $baseName             = $fs->getTubePressInstallationDirectoryBaseName();
        $jqueryUiCssUrl       = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.8.16.custom.css", $baseName);
        $wpOptionsPageCss     = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/css/wordpress-options-page.css", $baseName);
        $jqueryMultiSelectCss = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/css/jquery-ui-multiselect-widget/jquery.multiselect.css", $baseName);
        $jsColorUrl           = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/js/jscolor/jscolor.js", $baseName);
        $jqueryUiJsUrl        = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/js/jquery-ui/jquery-ui-1.8.16.custom.min.js", $baseName);
        $jqueryMultiSelectJs  = $wpFunctionWrapper->plugins_url("$baseName/sys/ui/static/js/jquery-ui-multiselect-widget/jquery.multiselect.min.js", $baseName);

        $wpFunctionWrapper->wp_register_style('jquery-ui-flick', $jqueryUiCssUrl);
        $wpFunctionWrapper->wp_register_style('tubepress-options-page', $wpOptionsPageCss);
        $wpFunctionWrapper->wp_register_style('jquery-ui-multiselect-widget', $jqueryMultiSelectCss);

        $wpFunctionWrapper->wp_enqueue_style('jquery-ui-flick');
        $wpFunctionWrapper->wp_enqueue_style('tubepress-options-page');
        $wpFunctionWrapper->wp_enqueue_style('jquery-ui-multiselect-widget');

        $wpFunctionWrapper->wp_register_script('jscolor-tubepress', $jsColorUrl);
        $wpFunctionWrapper->wp_register_script('jquery-ui-tubepress', $jqueryUiJsUrl);
        $wpFunctionWrapper->wp_register_script('jquery-ui-multiselect-widget', $jqueryMultiSelectJs);

        $wpFunctionWrapper->wp_enqueue_script('jquery-ui-tubepress');
        $wpFunctionWrapper->wp_enqueue_script('jquery-ui-multiselect-widget');
        $wpFunctionWrapper->wp_enqueue_script('jscolor-tubepress');
    }

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    public final function registerAdminMenuItem()
    {
        $wpFunctionWrapper = tubepress_impl_wordpress_WordPressServiceLocator::getWordPressFunctionWrapper();

        $wpFunctionWrapper->add_options_page('TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this, 'printOptionsPageHtml'));
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function printOptionsPageHtml()
    {
        /* grab the storage manager */
        $wpsm = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();

        /* initialize our options in case we need to */
        $wpsm->init();

        /* get the form handler */
        $optionsForm = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFormHandler();
        $hrps        = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();

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

    private static function _verifyNonce()
    {
        $wpFunctionWrapper = tubepress_impl_wordpress_WordPressServiceLocator::getWordPressFunctionWrapper();

    	$wpFunctionWrapper->check_admin_referer('tubepress-save', 'tubepress-nonce');
    }

    private static function _error($message)
    {
        if (is_array($message)) {

            $message = implode($message, '<br />');
        }

        echo '<div id="message" class="error fade"><p><strong>' . $message . '</strong></p></div>';
    }
}

