<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
abstract class TubePressPlayer
{
    const GREYBOX         = "greybox";
    const LIGHTWINDOW     = "lightwindow";
    const NORMAL         = "normal";
    const POPUP         = "popup";
    const SHADOWBOX     = "shadowbox";
    const YOUTUBE         = "youtube";
    
    /*
     * for each player, we want to know which CSS
     * and JS libraries that it needs
     */
    private $_cssLibs = array();
    private $_jsLibs = array();
    private $_preLoadHeaderJs = "";
    private $_postLoadHeaderJs = "";
    
    public final function getHeadContents() {
        $content = "";
        if ($this->_preLoadHeaderJs != "") {
            $content .= "<script type=\"text/javascript\">" . $this->_preLoadHeaderJs . "</script>";
        }
        
        foreach ($this->_jsLibs as $jsLib) {
            $content .= "<script type=\"text/javascript\" src=\"" . $jsLib . "\"></script>";
        }
        
        if ($this->_postLoadHeaderJs != "") {
            $content .= "<script type=\"text/javascript\">" . $this->_postLoadHeaderJs . "</script>";
        }
        
        foreach ($this->_cssLibs as $cssLib) {
            $content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
                " type=\"text/css\" />";
        }
        return $content;
    }
    
    protected final function setPostLoadJs($extraJS) {
        if (!is_string($extraJS)) {
            throw new Exception("Postload JS must be a string");
        }
        $this->_postLoadHeaderJs = $extraJS;
    }
    
    protected final function setPreLoadJs($extraJS) {
        if (!is_string($extraJS)) {
            throw new Exception("Preload JS must be a string");
        }
        $this->_preLoadHeaderJs = $extraJS;
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
    
    public abstract function getPlayLink(TubePressVideo $vid, TubePressOptionsManager $tpom);
    
    public static function getInstance($name) {
        switch ($name) {
            case TubePressPlayer::NORMAL:
                return new TPNormalPlayer();
                break;
            case TubePressPlayer::GREYBOX:
                return new TPGreyBoxPlayer();
                break;
            case TubePressPlayer::POPUP:
                return new TPPopupPlayer();
                break;
            case TubePressPlayer::YOUTUBE:
                return new TPYouTubePlayer();
                break;
            case TubePressPlayer::LIGHTWINDOW:
                return new TPlightWindowPlayer();
            case TubePressPlayer::SHADOWBOX:
                return new TPShadowBoxPlayer();
            default:
                throw new Exception("No such player with name '" . $name . "'");
            
        }
    }
}
?>
