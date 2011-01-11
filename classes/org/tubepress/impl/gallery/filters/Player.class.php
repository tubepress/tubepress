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

/**
 * Handles applying the player HTML to the gallery template.
 */
class org_tubepress_impl_gallery_filters_Player
{
    public function filter($template)
    {
        $tpom       = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $playerName = $tpom->get(org_tubepress_api_const_options_Display::CURRENT_PLAYER_NAME);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Applying HTML for <tt>%s</tt> player to the template', $playerName);

        $player     = $ioc->get('org_tubepress_api_player_Player');
        $playerHtml = $player->getHtml($videos[0], $galleryId);

        $template->setVariable(org_tubepress_api_template_Template::PLAYER_HTML, $playerHtml);
        $template->setVariable(org_tubepress_api_template_Template::PLAYER_NAME, $playerName);
        
        return $template;
    }
}
