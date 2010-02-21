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
tubepress_load_classes(array('org_tubepress_player_AbstractPlayer',
    'org_tubepress_video_Video',
    'org_tubepress_options_category_Embedded',
    'org_tubepress_embedded_EmbeddedPlayerService'));

/**
 * Plays videos in a modal window
 */
class org_tubepress_player_impl_ModalPlayer extends org_tubepress_player_AbstractPlayer
{
    public function getPreGalleryHtml(org_tubepress_video_Video $vid, $galleryId)
    {
        $tpom = $this->getOptionsManager();
        $eps = $this->getContainer()->safeGet($tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) . "-embedded", 
            org_tubepress_embedded_EmbeddedPlayerService::DDEFAULT . "-embedded");
        $this->getTemplate()->setVariable(org_tubepress_template_Template::EMBEDDED_SOURCE, $eps->toString($vid->getId()));
        $this->getTemplate()->setVariable(org_tubepress_template_Template::GALLERY_ID, $galleryId);
        return $this->getTemplate()->toString();    
    }
}
