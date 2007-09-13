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

class_exists("TubePressPlayer")
    || require(dirname(__FILE__) . "/../abstract/TubePressPlayer.php");

/**
 * Plays videos by themselves in a new window
 */
class TPNewWindowPlayer extends TubePressPlayer
{
	/**
	 * Default constructor
	 */
	function TPNewWindowPlayer() {
		$this->_title = _tpMsg("PLAYIN_NW_TITLE");
		$this->_cssLibs = array();
		$this->_jsLibs = array();
		$this->_extraJS = "";
	}
	
	/**
	 * Tells the gallery how to make the play link
	 */
    function getPlayLink($vid, $options)
	{	    
	    $url = new Net_URL(TubePressStatic::fullURL());
        $url->addQueryString(TP_PARAM_VID, $vid->getId());
        return sprintf('href="%s"', $url->getURL());
	}
}
?>
