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
tubepress_load_classes(array('org_tubepress_video_feed_inspection_FeedInspectionService'));

/**
 * Examines the feed from YouTube
 *
 */
class org_tubepress_video_feed_inspection_YouTubeFeedInspectionService implements org_tubepress_video_feed_inspection_FeedInspectionService
{   
    const NS_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/';
    
    public function getTotalResultCount($rawFeed)
    {
	   $dom = $this->_getDom($rawFeed);
        $result = $dom->getElementsByTagNameNS(org_tubepress_video_feed_inspection_YouTubeFeedInspectionService::NS_OPENSEARCH,
            'totalResults')->item(0)->nodeValue;
        
        $this->_makeSureNumeric($result);
        return $result;
    }
    
    public function getQueryResultCount($rawFeed)
    {
	    $dom = $this->_getDom($rawFeed);
        $result = $dom->getElementsByTagName('entry')->length;
        $this->_makeSureNumeric($result);
        return $result;
    }
    
    private function _getDom($rawFeed) {
        if (!class_exists('DOMDocument')) {
            throw new Exception('DOMDocument class not found');
        }
    	$dom = new DOMDocument();
    	if ($dom->loadXML($rawFeed) === FALSE) {
                throw new Exception('Problem parsing XML from YouTube');
    	}
    	return $dom;
    }

    private function _makeSureNumeric($result)
    {
        if (is_numeric($result) === FALSE) {
            throw new Exception("YouTube returned a non-numeric total result count: $result");
        }    
    }
}
