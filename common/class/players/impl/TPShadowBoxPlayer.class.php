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
 * Plays videos with GreyBox
 */
class TPShadowBoxPlayer extends AbstractTubePressPlayer
{    
    /**
     * Sets JS to be executed after the document has loaded
     *
     * @return void
     */
    protected function getPostLoadJS()
    {
    	return sprintf(<<<EOT
YAHOO.util.Event.onDOMReady(function() { 
    var options = { assetURL: "%s" };
    Shadowbox.init(options);
});
EOT
		, $this->_getBaseUrl());
    }
    
    /**
     * Sets the JS libraries to include
     *
     * @return void
     */
    protected function getJSLibs()
    {
    	$sbUrl = $this->_getBaseUrl();
    	return array(
            $sbUrl . "src/js/lib/yui-utilities.js",
            $sbUrl . "src/js/adapter/shadowbox-yui.js",
            $sbUrl . "src/js/shadowbox.js");
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
    	return array($this->_getBaseUrl() . "src/css/shadowbox.css");
    }
    
    /**
     * Tells the gallery how to play videos in ShadowBox.js
     *
     * @param TubePressVideo          $vid  The video to be played
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(TubePressVideo $vid, TubePressOptionsManager $tpom)
    {
        global $tubepress_base_url;
        
        $title  = $vid->getTitle();
        $height = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width  = $tpom->get(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
        $url = new Net_URL2($tubepress_base_url . "/common/ui/popup.php");
        $url->setQueryVariable("id", $vid->getId());
        $url->setQueryVariable("opts", $this->getEmbeddedPlayerService()->packOptionsToString($vid, $tpom));
        
        return sprintf('href="%s" title="%s" ' .
            'rel="shadowbox;height=%s;width=%s"',
		$url->getURL(true), $title, $height, $width); 
   }
   
   private function _getBaseUrl()
   {
   		global $tubepress_base_url;

        return $tubepress_base_url . "/lib/shadowbox/";
   }
}
?>
