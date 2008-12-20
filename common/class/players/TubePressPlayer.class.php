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
    public final function getHeadContents()
    {
        $content = "";
        if ($this->getPreLoadJs() != "") {
            $content .= "<script type=\"text/javascript\">" . 
                $this->getPreLoadJS() . "</script>";
        }
        
        $jsLibs = $this->getJSLibs();
        foreach ($jsLibs as $jsLib) {
            $content .= "<script type=\"text/javascript\" src=\"" . 
                $jsLib . "\"></script>";
        }
        
        if ($this->getPostLoadJS() != "") {
            $content .= "<script type=\"text/javascript\">" . 
                $this->getPostLoadJS() . "</script>";
        }
        
        $cssLibs = $this->getCSSLibs();
        foreach ($cssLibs as $cssLib) {
            $content .= "<link rel=\"stylesheet\" href=\"" . $cssLib . "\"" .
                " type=\"text/css\" />";
        }
        return $content;
    }
    
    /**
     * Sets JS to be executed after the document has loaded
     *
     * @return void
     */
    protected abstract function getPostLoadJS();
    
    /**
     * Enter description here...
     *
     * @param unknown_type $extraJS The text of the JS to run
     * 
     * @return void
     */
    protected abstract function getPreLoadJs();
    
    /**
     * Sets the JS libraries to include
     *
     * @return void
     */
    protected abstract function getJSLibs();
    
    /**
     * Sets the CSS libraries to include
     *
     * @param array $cssLibs An array of CSS libs to include
     * 
     * @return void
     */
    protected abstract function getCSSLibs();
    
    /**
     * Tells the gallery how to play the videos
     *
     * @param TubePressVideo          $vid  The video to play
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public abstract function getPlayLink(TubePressVideo $vid, 
        TubePressOptionsManager $tpom);
        
    public abstract function getPreGalleryHtml(TubePressVideo $vid,
    	TubePressOptionsManager $tpom);
    
    /**
     * Gets a new player instance
     *
     * @param string $name The name of the TubePressPlayer to instantiate
     * 
     * @return TubePressPlayer an instance of the player
     */
    public static function getInstance($name)
    {
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
