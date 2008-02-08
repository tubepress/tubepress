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
abstract class TubePressPlayer implements TubePressValue,
    TubePressHasTitle, TubePressHasName
{
	const newWindow = "new_window";
	const youTube = "youtube";
	const normal = "normal";
	const popup = "popup";
	const greyBox = "greybox";
	const lightWindow = "lightwindow";
	
	private $title;
	private $value;
	private $name;
	
	/*
	 * for each player, we want to know which CSS
	 * and JS libraries that it needs
	 */
	private $_cssLibs = array();
	private $_jsLibs = array();
	private $_extraJS = array();
	
	public final function getHeadContents() {
    	$content = "";
	    if ($this->_extraJS != "") {
        	$content .= "<script type=\"text/javascript\">" . $this->_extraJS . "</script>";
        }
    	
    	foreach ($this->_jsLibs as $jsLib) {
    		$content .= "<script type=\"text/javascript\" src=\"" . $jsLib . "\"></script>";
    	}
    	
    	foreach ($this->_cssLibs as $cssLib) {
    		$content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
            	" type=\"text/css\" />";
    	}
    	return $content;
	}
	
	protected final function setExtraJS($extraJS) {
	    if (!is_a($jsLibs, "string")) {
	        throw new Exception("Extra JS must be a string");
	    }
	    $this->_extraJS = $extraJS;
	}
	
	protected final function setJSLibs($jsLibs) {
	    if (!is_array($jsLibs)) {
	        throw new Exception("JS libraries must be an array");
	    }
	    $this->_jsLibs = $jsLibs;
	}
	
	protected final function setCSSLibs($cssLibs) {
	    if (!is_array($cssLibs)) {
	        throw new Exception("CSS libraries must be an array");
	    }
	    $this->_cssLibs = $cssLibs;
	}
	
	protected final function setTitle($newTitle) {
	    $this->title = $newTitle;
	}
	
	protected final function setName($newName) {
	    $this->name = $newName;
	}
	
	public final function getTitle() { return $this->title; }
	public final function getName() { return $this->name; }
	
	public abstract function getPlayLink(TubePressVideo $vid, $height, $width);
	
	public static function getInstance($name) {
	    switch ($name) {
	        case TubePressPlayer::normal:
	            return new TPNormalPlayer();
	            break;
	        default:
	            throw new Exception("No such player with name '" . $name . "'");
	        
	    }
	}
}
?>
