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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_url_UrlBuilder',
    'org_tubepress_video_feed_provider_Provider'));

/**
 * Builds URLs based on the current provider
 *
 */
class org_tubepress_url_DelegatingUrlBuilder implements org_tubepress_url_UrlBuilder
{
    private $_tpom;
    private $_youtubeBuilder;
    private $_vimeoBuilder;
    
    /**
     * Builds a URL for a list of videos
     *
     * @return string The request URL for this gallery
     */
    public function buildGalleryUrl($currentPage)
    {
        $provider = $this->_tpom->calculateCurrentVideoProvider();
        if ($provider === org_tubepress_video_feed_provider_Provider::VIMEO) {
            return $this->_vimeoBuilder->buildGalleryUrl($currentPage);
        }
        return $this->_youtubeBuilder->buildGalleryUrl($currentPage);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     */
    public function buildSingleVideoUrl($id)
    {
        $provider = $this->_tpom->calculateCurrentVideoProvider();
        if ($provider === org_tubepress_video_feed_provider_Provider::VIMEO) {
            return $this->_vimeoBuilder->buildSingleVideoUrl($id);
        }
        return $this->_youtubeBuilder->buildSingleVideoUrl($id);
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $tpom) { $this->_tpom = $tpom; }
    public function setYouTubeUrlBuilder(org_tubepress_url_UrlBuilder $ytb) { $this->_youtubeBuilder = $ytb; }
    public function setVimeoUrlBuilder(org_tubepress_url_UrlBuilder $vb) { $this->_vimeoBuilder = $vb; }
}
