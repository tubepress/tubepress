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
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
interface org_tubepress_player_Player
{
    const GREYBOX     = "greybox";
    const LIGHTWINDOW = "lightwindow";
    const NORMAL      = "normal";
    const POPUP       = "popup";
    const SHADOWBOX   = "shadowbox";
    const YOUTUBE     = "youtube";
    
    /**
     * Puts JS and CSS libraries in the head
     *
     * @return void
     */
    public function getHeadContents();
    
    /**
     * Tells the gallery how to play the videos
     *
     * @param org_tubepress_video_Video          $vid  The video to play
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom);
        
    public function getPreGalleryHtml(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom);
}
?>
