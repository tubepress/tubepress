<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_factory_strategies_AbstractFactoryStrategy'));

/**
 * Video factory for Vimeo
 */
class org_tubepress_impl_factory_strategies_VimeoFactoryStrategy extends org_tubepress_impl_factory_strategies_AbstractFactoryStrategy
{
    const LOG_PREFIX = 'Vimeo Factory Strategy';

    protected $_videoArray;
    
    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    function canHandle()
    {
        /* grab the arguments */
        $args = func_get_args();
        self::_checkArgs($args);
        $feed = $args[0];
        
        try {
            $unserialized = org_tubepress_impl_factory_strategies_AbstractFactoryStrategy::_unserializePhpFeed($feed);
        } catch (Exception $e) {
            return false;
        }

        return $unserialized->stat === 'ok';
    }
    
    protected function _preExecute($feed)
    {
        $unserialized = org_tubepress_impl_factory_strategies_AbstractFactoryStrategy::_unserializePhpFeed($feed);
        
        if (isset($unserialized->video)) {
            $this->_videoArray = $unserialized->video;
            return;
        }
        
        if (isset($unserialized->videos) && isset($unserialized->videos->video)) {
            $this->_videoArray = $unserialized->videos->video;
            return;    
        }
        $this->_videoArray = array();
    }
    
    protected function _postExecute($feed)
    {
        unset($this->_videoArray);
    }
    
    protected function _countVideosInFeed($feed)
    {
        return sizeof($this->_videoArray);
    }
    
    protected function _getAuthorDisplayName($index)
    {
        return $this->_videoArray[$index]->owner->display_name;
    }
    
    protected function _getAuthorUid($index)
    {
        return $this->_videoArray[$index]->owner->username;
    }
    
    protected function _getCategory($index)
    {
        return '';
    }
    
    protected function _getRawCommentCount($index)
    {
        return '';
    }
    
    protected function _getDescription($index)
    {
        return $this->_videoArray[$index]->description;    
    }
    
    protected function _getDurationInSeconds($index)
    {
        return $this->_videoArray[$index]->duration;
    }
    
    protected function _getHomeUrl($index)
    {
        return 'http://vimeo.com/' . $this->_videoArray[$index]->id;
    }
    
    protected function _getId($index)
    {
        return $this->_videoArray[$index]->id;    
    }
    
    protected function _getKeywordsArray($index)
    {
        return self::_gatherArrayOfContent($this->_videoArray[$index], 'tags', 'tag');
    }
    
    protected function _getRawLikeCount($index)
    {
        return $this->_videoArray[$index]->number_of_likes;
    }
    
    protected function _getRatingAverage($index)
    {
        return '';
    }
    
    protected function _getRawRatingCount($index)
    {
        return '';   
    }
    
    protected function _getThumbnailUrlsArray($index)
    {
        $raw = self::_gatherArrayOfContent($this->_videoArray[$index], 'thumbnails', 'thumbnail');
        return array($raw[0]);
    }
    
    protected function _getTimeLastUpdatedInUnixTime($index)
    {
        return '';   
    }
    
    protected function _getTimePublishedInUnixTime($index)
    {
        return strtotime($this->_videoArray[$index]->upload_date);
    }
    
    protected function _getTitle($index)
    {
        return $this->_videoArray[$index]->title;
    }
    
    protected function _getRawViewCount($index)
    {
        return $this->_videoArray[$index]->number_of_plays;
    }
    
    protected function _canHandleVideo($index)
    {
        //Vimeo never sends us videos we can't display
        return true;
    }
    
    protected static function _gatherArrayOfContent($node, $firstDimension, $secondDimension)
    {
        $results = array();
        
        if (isset($node->$firstDimension) && is_array($node->$firstDimension->$secondDimension)) {
            foreach ($node->$firstDimension->$secondDimension as $item) {
                $results[] = $item->_content;
            }
        }
        
        return $results;
    }
}
