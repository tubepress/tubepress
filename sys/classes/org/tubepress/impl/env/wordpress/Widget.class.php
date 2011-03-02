<?php
/**
Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)

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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_message_WordPressMessageService',
    'org_tubepress_impl_ioc_FreeWordPressPluginIocService',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_const_options_names_Widget',
    'org_tubepress_impl_template_SimpleTemplate',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_ioc_ProInWordPressIocService',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_player_Player'));

class org_tubepress_impl_env_wordpress_Widget
{
    /**
     * Registers the TubePress widget with WordPress.
     *
     * @return void
     */
    public static function initAction()
    {
        $msg       = new org_tubepress_impl_message_WordPressMessageService();
        $widgetOps = array('classname' => 'widget_tubepress', 'description' => $msg->_('widget-description'));

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
        $tpom         = $iocContainer->get('org_tubepress_api_options_OptionsManager');
        $parser       = $iocContainer->get('org_tubepress_api_shortcode_ShortcodeParser');
        $gallery      = $iocContainer->get('org_tubepress_api_html_HtmlGenerator');

        /* Turn on logging if we need to */
        org_tubepress_impl_log_Log::setEnabled($tpom->get(org_tubepress_api_const_options_names_Advanced::DEBUG_ON), $_GET);

        /* default widget options */
        $defaultWidgetOptions = array(
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE    => 3,
            org_tubepress_api_const_options_names_Meta::VIEWS                  => false,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION            => true,
            org_tubepress_api_const_options_names_Display::DESC_LIMIT          => 50,
            org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_player_Player::POPUP,
            org_tubepress_api_const_options_names_Display::THUMB_HEIGHT        => 105,
            org_tubepress_api_const_options_names_Display::THUMB_WIDTH         => 135,
            org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE      => false,
            org_tubepress_api_const_options_names_Display::PAGINATE_BELOW      => false,
            org_tubepress_api_const_options_names_Display::THEME               => 'sidebar'
        );

        /* now apply the user's options */
        $wpsm = $iocContainer->get('org_tubepress_api_options_OptionsManager');
        $parser->parse($wpsm->get(org_tubepress_api_const_options_names_Widget::TAGSTRING));

        /* calculate the final options */
        $finalOptions = array_merge($defaultWidgetOptions, $tpom->getCustomOptions());
        $tpom->setCustomOptions($finalOptions);

        if ($tpom->get(org_tubepress_api_const_options_names_Display::THEME) === '') {
            $tpom->set(org_tubepress_api_const_options_names_Display::THEME, 'sidebar');
        }

        $out = $gallery->getHtmlForShortcode('');

        /* do the standard WordPress widget dance */
        echo $before_widget . $before_title .
            $wpsm->get(org_tubepress_api_const_options_names_Widget::TITLE) .
            $after_title . $out . $after_widget;
    }

    /**
     * Prints the TubePress widget control panel.
     *
     * @return void
     */
    public static function printControlPanel()
    {
        $iocContainer = new org_tubepress_impl_ioc_FreeWordPressPluginIocService();
        $wpsm         = $iocContainer->get('org_tubepress_api_options_StorageManager');
        $msg          = $iocContainer->get('org_tubepress_api_message_MessageService');

        /* are we saving? */
        if (isset($_POST['tubepress-widget-submit'])) {
            $wpsm->set(org_tubepress_api_const_options_names_Widget::TAGSTRING, strip_tags(stripslashes($_POST['tubepress-widget-tagstring'])));
            $wpsm->set(org_tubepress_api_const_options_names_Widget::TITLE, strip_tags(stripslashes($_POST['tubepress-widget-title'])));
        }

        /* load up the gallery template */
        $tpl = new org_tubepress_impl_template_SimpleTemplate();
        $tpl->setPath(dirname(__FILE__) . '/../../../../../../../sys/ui/templates/wordpress/widget_controls.tpl.php');

        /* set up the template */
        $tpl->setVariable(org_tubepress_api_const_template_Variable::WIDGET_CONTROL_TITLE, $msg->_('options-meta-title-title'));
        $tpl->setVariable(org_tubepress_api_const_template_Variable::WIDGET_TITLE, $wpsm->get(org_tubepress_api_const_options_names_Widget::TITLE));
        $tpl->setVariable(org_tubepress_api_const_template_Variable::WIDGET_CONTROL_SHORTCODE, $msg->_('widget-tagstring-description'));
        $tpl->setVariable(org_tubepress_api_const_template_Variable::WIDGET_SHORTCODE, $wpsm->get(org_tubepress_api_const_options_names_Widget::TAGSTRING));

        /* get the template's output */
        echo $tpl->toString();
    }
}

