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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_video_Video'));

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
interface org_tubepress_api_player_Player
{
    const NORMAL    = 'normal';
    const POPUP     = 'popup';
    const SHADOWBOX = 'shadowbox';
    const JQMODAL   = 'jqmodal';
    const YOUTUBE   = 'youtube';
    const TINYBOX   = 'tinybox';
    const FANCYBOX  = 'fancybox';
    const STATICC   = 'static';
    const SOLO      = 'solo';
    const VIMEO     = 'vimeo';

    /**
     * Get's the HTML for the TubePress "player"
     *
     * @param org_tubepress_api_video_Video $vid       The video to display in the player.
     * @param unknown                       $galleryId The current gallery ID.
     * 
     * @throws Exception If something goes wrong.
     *
     * @return string The HTML for this player with the given video.
     */
    function getHtml(org_tubepress_api_video_Video $vid, $galleryId);
}
