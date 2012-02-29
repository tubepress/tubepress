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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_player_PlayerHtmlGenerator',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_template_Template',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_ioc_IocContainer'
));

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
class org_tubepress_impl_player_DefaultPlayerHtmlGenerator implements org_tubepress_api_player_PlayerHtmlGenerator
{
    const LOG_PREFIX = 'Player HTML Generator';

    public function getHtml(org_tubepress_api_video_Video $vid)
    {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context       = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $pm            = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $playerName    = $context->get(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $template      = $themeHandler->getTemplateInstance("players/$playerName.tpl.php");
        $providerName  = $pc->calculateProviderOfVideoId($vid->getId());

        $template = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_PLAYER,
                            $template, $vid, $providerName, $playerName);

        $html = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_PLAYER, $template->toString(), $vid, $providerName, $playerName);
        $html = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_ANY, $html);

        return $html;
    }
}
