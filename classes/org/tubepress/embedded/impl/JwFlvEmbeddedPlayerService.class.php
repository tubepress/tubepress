<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
tubepress_load_classes(array('org_tubepress_embedded_impl_AbstractEmbeddedPlayerService',
    'net_php_pear_Net_URL2',
    'org_tubepress_template_Template'));

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
   
        $tpom = $this->getOptionsManager();
        
        $link = new net_php_pear_Net_URL2(sprintf('http://www.youtube.com/watch?v=%s', $videoId));
        
        $link = $link->getURL(true);
        
        $this->_template->setVariable(org_tubepress_template_Template::TUBEPRESS_BASE_URL, $tubepress_base_url);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_DATA_URL,  $link);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_AUTOSTART, $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY) ? 'true' : 'false');
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH,     $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_HEIGHT,    $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT));
        
        return $this->_template->toString();
    }  
}
