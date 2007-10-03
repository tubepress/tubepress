<?php
/**
 * TubePressPlayer.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress
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
 */

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
abstract class TubePressPlayer extends TPAbstractHasTitle implements TubePressValue
{
	define("TP_PLAYIN_NW", "new_window");
define("TP_PLAYIN_YT", "youtube");
define("TP_PLAYIN_NORMAL", "normal");
define("TP_PLAYIN_POPUP", "popup");
define("TP_PLAYIN_GREYBOX", "greybox");
define("TP_PLAYIN_LWINDOW", "lightwindow");
	
	/*
	 * for each player, we want to know which CSS
	 * and JS libraries that it needs
	 */
	protected $_cssLibs = array();
	protected $_jsLibs = array();
	protected $_extraJS = array();
	
	public final function getJS() { return $this->_jsLibs; }
	
	/**
	 * @return An array of the FQ CSS libraries
	 */
	public final function getCss() { return $this->_cssLibs; }
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public final function getExtraJS() { return $this->_extraJS; }
	
	public abstract function getPlayLink();
}
?>
