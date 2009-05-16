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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('net_php_pear_HTML_Template_IT',
    'org_tubepress_embedded_impl_AbstractEmbeddedPlayerService',
    'net_php_pear_Net_URL2',
    'org_tubepress_options_category_Embedded'));

/**
 * Represents an HTML-embeddable YouTube player
 *
 */
class org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/embedded/youtube/html_templates");
        if (!$tpl->loadTemplatefile("object.tpl.html", true, true)) {
            throw new Exception("Couldn't load embedded template");
        }    
        
        $link = new net_php_pear_Net_URL2(sprintf("http://www.youtube.com/v/%s", $videoId));
        
        $tpom = $this->getOptionsManager();
        
        $color1      = $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_COLOR), "999999");
        $color2      = $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT), "FFFFFF");
        $showRelated = $tpom->get(org_tubepress_options_category_Embedded::SHOW_RELATED);
        $autoPlay    = $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY);
        $loop        = $tpom->get(org_tubepress_options_category_Embedded::LOOP);
        $genie       = $tpom->get(org_tubepress_options_category_Embedded::GENIE);
        $border      = $tpom->get(org_tubepress_options_category_Embedded::BORDER);
        $width       = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $height      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $quality     = $tpom->get(org_tubepress_options_category_Embedded::QUALITY);
        $fullscreen  = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);
        $showInfo    = $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO);
   
        if (!($color1 == "999999" && $color2 == "FFFFFF")) {
            $link->setQueryVariable("color2", "0x" . $color1);
            $link->setQueryVariable("color1", "0x" . $color2);
        }
        $link->setQueryVariable("rel", $showRelated   ? "1" : "0");
        $link->setQueryVariable("autoplay", $autoPlay ? "1" : "0");
        $link->setQueryVariable("loop", $loop         ? "1" : "0");
        $link->setQueryVariable("egm", $genie         ? "1" : "0");
        $link->setQueryVariable("border", $border     ? "1" : "0");
        $link->setQueryVariable("fs", $fullscreen     ? "1" : "0");
        
        $link->setQueryVariable("showinfo", $showInfo ? "1" : "0");
        
        switch ($quality) {
        case "high":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "6");
            break;
        case "higher":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "18");
            break;
        case "highest":
            $link->setQueryVariable("ap", "%26");
            $link->setQueryVariable("fmt", "22");
            break;      
        }
        
        $link = $link->getURL(true);

        $tpl->setVariable('URL', $link);
        $tpl->setVariable('WIDTH', $width);
        $tpl->setVariable('HEIGHT', $height);
        $tpl->setVariable('FULLSCREEN', $fullscreen ? "true" : "false");
        
        $embedSrc = $tpl->get();
     
        return str_replace("?", "&amp;", $embedSrc);
    }
}