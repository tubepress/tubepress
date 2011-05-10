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
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_player_Player',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_api_template_Template',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Handles applying the player HTML to the gallery template.
 */
class org_tubepress_impl_plugin_gallerytemplate_Player
{
    public function alter_galleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $providerResult, $galleryId)
    {
        $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom       = $ioc->get('org_tubepress_api_options_OptionsManager');
        $player     = $ioc->get('org_tubepress_api_player_Player');
        $playerName = $tpom->get(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
        $videos     = $providerResult->getVideoArray();
        $playerHtml = $player->getHtml($videos[0], $galleryId);

        $template->setVariable(org_tubepress_api_const_template_Variable::PLAYER_HTML, $playerHtml);
        $template->setVariable(org_tubepress_api_const_template_Variable::PLAYER_NAME, $playerName);
        
        return $template;
    }
}
