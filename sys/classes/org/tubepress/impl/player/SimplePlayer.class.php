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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_player_Player',
    'org_tubepress_impl_ioc_IocContainer'));

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
class org_tubepress_impl_player_SimplePlayer implements org_tubepress_api_player_Player
{
    const LOG_PREFIX = 'Player';
    
    public function getHtml(org_tubepress_api_video_Video $vid, $galleryId)
    {
        $ioc             = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom            = $ioc->get('org_tubepress_api_options_OptionsManager');
        $playerName      = $tpom->get(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
        $eps             = $ioc->get('org_tubepress_api_embedded_EmbeddedPlayer');
        $themeHandler    = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        
        try {
            $template   = $themeHandler->getTemplateInstance("players/$playerName.tpl.php");
        } catch (Exception $e) {
            return '';
        }
        
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, $eps->toString($vid->getId()));
        $template->setVariable(org_tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO, $vid);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $tpom->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));
        
        return $template->toString();
    }
}
