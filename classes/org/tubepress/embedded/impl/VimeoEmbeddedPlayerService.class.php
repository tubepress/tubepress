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
tubepress_load_classes(array(
    'org_tubepress_embedded_impl_AbstractEmbeddedPlayerService'));

/**
 * An HTML-embeddable player
 *
 */
class org_tubepress_embedded_impl_VimeoEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @param $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {   
        $link = new net_php_pear_Net_URL2('http://vimeo.com/moogaloop.swf');
        
        $tpom = $this->getOptionsManager();
        
        $width       = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $height      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $fullscreen  = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);

        $link->setQueryVariable('clip_id', $videoId);
        $link->setQueryVariable('fullscreen', $fullscreen     ? '1' : '0');
        
        $link = $link->getURL(true);

        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_DATA_URL, $link);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH, $width);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_HEIGHT, $height);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_FULLSCREEN, $fullscreen ? 'true' : 'false');
        
        $embedSrc = $this->_template->toString();
     
        return $embedSrc;
    }
}
