<?php
/**
 * TPPopupPlayer.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * Plays videos in an HTML popup window
 */
class TPPopupPlayer extends TubePressPlayer
{
    public function __construct() {
        $this->setName(TubePressPlayer::popup);
        $this->setTitle("in a popup window");
    }
	
	/**
	 * Tells the gallery how to play the videos
	 */
	public function getPlayLink(TubePressVideo $vid, TubePressStorage_v157 $stored)
	{
	    global $tubepress_base_url;

	    $title = $vid->getTitle();
	    $height = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedHeight);
	    $width = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedWidth);
	    $embed = new TubePressEmbeddedPlayer($vid, $stored);
		
	    $url = new Net_URL($tubepress_base_url . "/common/ui/popup.php");
	    $url->addQueryString("embed", $embed->toString());
	    $url->addQueryString("name", $title);
	    
	    return "href='#' onclick='tubePress_popup(" .
            '"' . $url->getURL() . '",' . $height . ',' . $width . ')\''; 
	}
}
?>
