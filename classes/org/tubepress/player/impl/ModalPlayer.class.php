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
tubepress_load_classes(array('org_tubepress_player_AbstractPlayer',
    'org_tubepress_video_Video',
    'net_php_pear_HTML_Template_IT',
    'org_tubepress_options_category_Embedded'));

/**
 * Plays videos in a modal window
 */
class org_tubepress_player_impl_ModalPlayer extends org_tubepress_player_AbstractPlayer
{
    public function getPreGalleryHtml(org_tubepress_video_Video $vid, $galleryId)
    {
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/players/shared/html_templates");
        if (!$tpl->loadTemplatefile("pre_gallery_modal.tpl.html", true, true)) {
            throw new Exception("Couldn't load pre gallery template");
        }

        $tpom = $this->getOptionsManager();
        $eps = $this->getContainer()->safeGet($tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) . "-embedded", 
            org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . "-embedded");
        $tpl->setVariable("EMBEDSRC", $eps->toString($vid->getId()));
        $tpl->setVariable('GALLERYID', $galleryId);
        return $tpl->get();    
    }
}
