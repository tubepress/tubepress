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
 * Plays videos at the top of a gallery
 */
class org_tubepress_player_impl_NormalPlayer extends org_tubepress_player_AbstractPlayer
{
    /**
     * Tells the gallery how to play videos with the normal player
     *
     * @param org_tubepress_video_Video          $vid  The video to be played
     * @param org_tubepress_options_manager_OptionsManager $tpom The TubePress options manager
     * 
     * @return string The play link attributes
     */
    public function getPlayLink(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {
        $title  = $vid->getTitle();
        $width  = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);

        $this->getEmbeddedPlayerService()->applyOptions($vid, $tpom);
        
        return sprintf(<<<EOT
href="#" onclick="tubePress_normalPlayer('%s', '%d', '%s')"
EOT
            , rawurlencode($this->getEmbeddedPlayerService()->toString()), $width, rawurlencode($title));
    }
    
    public function getPreGalleryHtml(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/players/normal/html_templates");
        if (!$tpl->loadTemplatefile("pre_gallery.tpl.html", true, true)) {
            throw new Exception("Couldn't load pre gallery template");
        }
        
        $this->getEmbeddedPlayerService()->applyOptions($vid, $tpom);
        
        $tpl->setVariable("EMBEDSRC", $this->getEmbeddedPlayerService()->toString());
        $tpl->setVariable("TITLE", $vid->getTitle());
        $tpl->setVariable("WIDTH", 
            $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        return $tpl->get();    
    }
}
?>
