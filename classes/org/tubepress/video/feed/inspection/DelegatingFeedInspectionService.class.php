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
tubepress_load_classes(array('org_tubepress_video_feed_inspection_FeedInspectionService',
    'org_tubepress_options_manager_OptionsManager'));

/**
 * Sends the feed to the right inspection service based on the provider
 *
 */
class org_tubepress_video_feed_inspection_DelegatingFeedInspectionService implements org_tubepress_video_feed_inspection_FeedInspectionService
{   
    private $_tpom;
    private $_youtubeInspectionService;
    private $_vimeoInspectionService;
    
    public function getTotalResultCount($rawFeed)
    {
	    $provider = $this->_tpom->calculateCurrentVideoProvider();
	    if ($provider === org_tubepress_video_feed_provider_Provider::VIMEO) {
	        return $this->_vimeoInspectionService->getTotalResultCount($rawFeed);
	    }
	    return $this->_youtubeInspectionService->getTotalResultCount($rawFeed);
    }
    
    public function getQueryResultCount($rawFeed)
    {
	    $provider = $this->_tpom->calculateCurrentVideoProvider();
        if ($provider === org_tubepress_video_feed_provider_Provider::VIMEO) {
            return $this->_vimeoInspectionService->getQueryResultCount($rawFeed);
        }
        return $this->_youtubeInspectionService->getQueryResultCount($rawFeed);
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $om) { $this->_tpom = $om; }
    public function setYouTubeInspectionService(org_tubepress_video_feed_inspection_FeedInspectionService $yfis) { $this->_youtubeInspectionService = $yfis; }
    public function setVimeoInspectionService(org_tubepress_video_feed_inspection_FeedInspectionService $yfis) { $this->_vimeoInspectionService = $yfis; }    
}
