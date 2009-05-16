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
    'net_php_pear_Net_URL2'));

/**
 * Represents an HTML-embeddable JW FLV Player
 *
 */
class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        global $tubepress_base_url;
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/embedded/longtail/html_templates");
        if (!$tpl->loadTemplatefile("object.tpl.html", true, true)) {
            throw new Exception("Couldn't load embedded template");
        }    
        $tpom = $this->getOptionsManager();
        
        $link = new net_php_pear_Net_URL2(sprintf("http://www.youtube.com/watch?v=%s", $videoId));
        
        $link = $link->getURL(true);
        
        $tpl->setVariable("TUBEPRESS_BASE", $tubepress_base_url);
        $tpl->setVariable("YOUTUBE_LINK", $link);
        $tpl->setVariable('AUTOSTART', $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY) ? 'true' : 'false');
        $tpl->setVariable('WIDTH', $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        $tpl->setVariable('HEIGHT', $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT));
        
        return $tpl->get();
    }  
}