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

/**
 * TubePress gallery utilities.
 */
class org_tubepress_gallery_TubePressGallery
{
    public function getHtml(org_tubepress_ioc_IocService $iocService, $shortCodeContent = '')
    {
        $tpom = $iocService->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);

        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {
            $this->parse($shortCodeContent, $tpom);
        }

        /* user wants to display a single video with meta info */
        if ($tpom->get(org_tubepress_options_category_Gallery::VIDEO) != '') {
            $videoId = $tpom->get(org_tubepress_options_category_Gallery::VIDEO);
            org_tubepress_log_Log::log($this->_logPrefix, 'Building single video with ID %s', $videoId);
            $singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);
            return $singleVideoGenerator->getSingleVideoHtml($videoId);
        }
        org_tubepress_log_Log::log($this->_logPrefix, 'No video ID set in shortcode.');

        /* see if the users wants to display just the video in the query string */
        $playerName = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        if ($playerName == org_tubepress_player_Player::SOLO) {
            org_tubepress_log_Log::log($this->_logPrefix, 'Solo player detected. Checking query string for video ID');
            $videoId = org_tubepress_querystring_QueryStringService::getCustomVideo($_GET);
            if ($videoId != '') {
                org_tubepress_log_Log::log($this->_logPrefix, 'Building single video with ID %s', $videoId);
                $singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);
                return $singleVideoGenerator->getSingleVideoHtml($videoId);
            }
            org_tubepress_log_Log::log($this->_logPrefix, 'Solo player in use, but no video ID set in URL. Will display a gallery instead.', $videoId);
        }

        $galleryId = org_tubepress_querystring_QueryStringService::getGalleryId($_GET);
        if ($galleryId == '') {
            $galleryId = mt_rand();
        }

        /* normal gallery */
        org_tubepress_log_Log::log($this->_logPrefix, 'Starting to build gallery %s', $galleryId);
        $gallery = $iocService->get(org_tubepress_ioc_IocService::GALLERY);
        return $gallery->getHtml($galleryId);
    }
}
