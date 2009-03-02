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
 * Represents an HTML-embeddable JW FLV Player
 *
 */
class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    private $_tpl;

    public function __construct()
    {
        $this->_tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/embedded/longtail/html_templates");
        if (!$this->_tpl->loadTemplatefile("object.tpl.html", true, true)) {
            throw new Exception("Couldn't load embedded template");
        }
    }

    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        global $tubepress_base_url;

        $link = new net_php_pear_Net_URL2(sprintf("http://www.youtube.com/watch?v=%s", $videoId));
        
        $link = $link->getURL(true);
        
        $this->_tpl->setVariable("TUBEPRESS_BASE", $tubepress_base_url);
        $this->_tpl->setVariable("YOUTUBE_LINK", $link);

        return $this->_tpl->get();
    }  
}

?>
