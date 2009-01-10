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
abstract class AbstractTubePressPlayer implements TubePressPlayer
{   
	private $_tpeps;
	
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
    protected function getPostLoadJS()
    {
    	return "";
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $extraJS The text of the JS to run
     * 
     * @return void
     */
    protected function getPreLoadJs()
    {
    	return "";
    }
    
    /**
     * Sets the JS libraries to include
     *
     * @return void
     */
    protected function getJSLibs()
    {
    	return array();
    }
    
    /**
     * Sets the CSS libraries to include
     *
     * @param array $cssLibs An array of CSS libs to include
     * 
     * @return void
     */
    protected function getCSSLibs()
    {
    	return array();
    }
    
    public final function setEmbeddedPlayerService(TubePressEmbeddedPlayerService $tpeps)
    {
    	$this->_tpeps = $tpeps;	
    }
    
    protected function getEmbeddedPlayerService()
    {
    	return $this->_tpeps;
    }
    
    public function getPreGalleryHtml(TubePressVideo $vid, TubePressOptionsManager $tpom)
    {
    	return "";
    }
}
?>
