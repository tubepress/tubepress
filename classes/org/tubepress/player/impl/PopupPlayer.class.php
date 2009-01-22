<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 * Plays videos in an HTML popup window
 */
class org_tubepress_player_impl_PopupPlayer extends org_tubepress_player_AbstractPlayer
{
    /**
     * Tells the gallery how to play videos in a popup window
     *
     * @param org_tubepress_video_Video          $vid  The video to be played
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {
        global $tubepress_base_url;

        $height = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $width  = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        
        $url = new net_php_pear_Net_URL2($tubepress_base_url . "/ui/players/popup.php");
        $url->setQueryVariable("id", $vid->getId());
        $url->setQueryVariable("opts", $this->getEmbeddedPlayerService()->packOptionsToString($vid, $tpom));
        
        return sprintf(<<<EOT
href="#" onclick="tubePress_popup('%s', %d, %d);"
EOT
            , $url->getURL(true), $height, $width);
    }
}
?>
