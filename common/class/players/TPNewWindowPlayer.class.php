<?php
/**
 * TPNewWindowPlayer.php
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
 * Plays videos by themselves in a new window
 */
class TPNewWindowPlayer extends TubePressPlayer
{
	const video_param = "tubepress_id";
	
    public function __construct() {
        $this->setName(TubePressPlayer::newWindow);
        $this->setTitle("in a new window by itself");
    }
	
	/**
	 * Tells the gallery how to make the play link
	 */
    function getPlayLink(TubePressVideo $vid, $height, $width)
	{	    
	    $url = new Net_URL(TubePressStatic::fullURL());
        $url->addQueryString(TPNewWindowPlayer::video_param, $vid->getId());
        return sprintf('href="%s"', $url->getURL());
	}
}
?>
