<?php
/**
Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)

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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Thumbs',
	'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_WordPress',
    'org_tubepress_api_const_options_values_PlayerLocationValue',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_ioc_FreeWordPressPluginIocService',
    'org_tubepress_impl_message_WordPressMessageService',
));

class org_tubepress_impl_env_wordpress_Widget
{
    const WIDGET_CONTROL_SHORTCODE = 'widgetControlShortcode';
    const WIDGET_CONTROL_TITLE     = 'widgetControlTitle';
    const WIDGET_SHORTCODE         = 'widgetShortcode';
    const WIDGET_TITLE             = 'widgetTitle';
    const WIDGET_SUBMIT_TAG        = 'tubepress-widget-submit';

    /**
     * Registers the TubePress widget with WordPress.
     *
     * @return void
     */
    public static function initAction()
    {
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $msg       = $ioc->get(org_tubepress_api_message_MessageService::_);
        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $msg->_('Displays YouTube or Vimeo videos with TubePress'));  //>(translatable)<

        wp_register_sidebar_widget('tubepress', 'TubePress', array('org_tubepress_impl_env_wordpress_Widget', 'printWidget'), $widgetOps);
        wp_register_widget_control('tubepress', 'TubePress', array('org_tubepress_impl_env_wordpress_Widget', 'printControlPanel'));
    }

    /**
     * Prints the output of the TubePress widget.
     *
     * @param array $opts The array of widget options.
     *
     * @return void
     */
    public static function printWidget($opts)
    {
        extract($opts);

        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context      = $iocContainer->get(org_tubepress_api_exec_ExecutionContext::_);
        $parser       = $iocContainer->get(org_tubepress_api_shortcode_ShortcodeParser::_);
        $gallery      = $iocContainer->get(org_tubepress_api_shortcode_ShortcodeHtmlGenerator::_);
        $ms           = $iocContainer->get(org_tubepress_api_message_MessageService::_);

        /* Turn on logging if we need to */
        org_tubepress_impl_log_Log::setEnabled($context->get(org_tubepress_api_const_options_names_Advanced::DEBUG_ON), $_GET);

        /* default widget options */
        $defaultWidgetOptions = array(
            org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE  => 3,
            org_tubepress_api_const_options_names_Meta::VIEWS               => false,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION         => true,
            org_tubepress_api_const_options_names_Meta::DESC_LIMIT          => 50,
            org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => org_tubepress_api_const_options_values_PlayerLocationValue::POPUP,
            org_tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT      => 105,
            org_tubepress_api_const_options_names_Thumbs::THUMB_WIDTH       => 135,
            org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE    => false,
            org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW    => false,
            org_tubepress_api_const_options_names_Thumbs::THEME             => 'sidebar',
            org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS      => false
        );

        /* now apply the user's options */
        $rawTag    = $context->get(org_tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $widgetTag = org_tubepress_impl_util_StringUtils::removeNewLines($rawTag);
        $parser->parse($widgetTag);

        /* calculate the final options */
        $finalOptions = array_merge($defaultWidgetOptions, $context->getCustomOptions());
        $context->setCustomOptions($finalOptions);

        if ($context->get(org_tubepress_api_const_options_names_Thumbs::THEME) === '') {
            $context->set(org_tubepress_api_const_options_names_Thumbs::THEME, 'sidebar');
        }

        try {

            $out = $gallery->getHtmlForShortcode('');

        } catch (Exception $e) {

            $out = $e->getMessage();
        }

        /* do the standard WordPress widget dance */
        echo $before_widget . $before_title .
            $context->get(org_tubepress_api_const_options_names_WordPress::WIDGET_TITLE) .
            $after_title . $out . $after_widget;

        /* reset the context for the next shortcode */
        $context->reset();
    }

    /**
     * Prints the TubePress widget control panel.
     *
     * @return void
     */
    public static function printControlPanel()
    {
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $wpsm         = $iocContainer->get(org_tubepress_api_options_StorageManager::_);
        $msg          = $iocContainer->get(org_tubepress_api_message_MessageService::_);
        $explorer     = $iocContainer->get(org_tubepress_api_filesystem_Explorer::_);
        $tplBuilder   = $iocContainer->get(org_tubepress_api_template_TemplateBuilder::_);
        $hrps         = $iocContainer->get(org_tubepress_api_http_HttpRequestParameterService::_);

        /* are we saving? */
        if ($hrps->hasParam(self::WIDGET_SUBMIT_TAG)) {

            self::_verifyNonce();

            $wpsm->set(org_tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE, $hrps->getParamValue('tubepress-widget-tagstring'));
            $wpsm->set(org_tubepress_api_const_options_names_WordPress::WIDGET_TITLE, $hrps->getParamValue('tubepress-widget-title'));
        }

        /* load up the gallery template */
        $templatePath = $explorer->getTubePressBaseInstallationPath() . '/sys/ui/templates/wordpress/widget_controls.tpl.php';
        $tpl          = $tplBuilder->getNewTemplateInstance($templatePath);

        /* set up the template */
        $tpl->setVariable(self::WIDGET_TITLE, $wpsm->get(org_tubepress_api_const_options_names_WordPress::WIDGET_TITLE));
        $tpl->setVariable(self::WIDGET_CONTROL_TITLE, $msg->_('Title'));                                                                                                            //>(translatable)<
        $tpl->setVariable(self::WIDGET_CONTROL_SHORTCODE, $msg->_('TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.')); //>(translatable)<
        $tpl->setVariable(self::WIDGET_SHORTCODE, $wpsm->get(org_tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE));
        $tpl->setVariable(self::WIDGET_SUBMIT_TAG, self::WIDGET_SUBMIT_TAG);

        /* get the template's output */
        echo $tpl->toString();
    }

    private static function _verifyNonce() {

        check_admin_referer('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
    }
}

