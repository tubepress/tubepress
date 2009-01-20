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
 * Plays videos from the original YouTube page
 */
class org_tubepress_player_impl_YouTubePlayer extends org_tubepress_player_AbstractPlayer
{
    /**
     * Tells the gallery how to play videos on YouTube
     *
     * @param org_tubepress_video_Video          $vid  The video to be played
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    function getPlayLink(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {   
        $link = new Net_URL2(sprintf('href="http://youtube.com/watch?v=%s"',
            $vid->getId()));
        
        switch ($tpom->get(org_tubepress_options_category_Embedded::QUALITY)) {
            case "high":
                $link->setQueryVariable("fmt", "6");
                break;
            case "higher":
                $link->setQueryVariable("fmt", "18");
                break;
            case "highest":
                $link->setQueryVariable("fmt", "22");
                break;      
        }
        
        return $link->getURL();
    }
}
?>
