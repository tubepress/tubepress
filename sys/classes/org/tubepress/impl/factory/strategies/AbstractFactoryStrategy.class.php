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
tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_video_Video',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_util_TimeUtils'));

/**
 * Base class for factory strategies.
 */
abstract class org_tubepress_impl_factory_strategies_AbstractFactoryStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Abstract Factory Strategy';
    
    private $_tpom;
    
    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    function start()
    {
        return;
    }
    
    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    function stop()
    {
        return;
    }
    
    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    function execute()
    {
        /* grab the arguments */
        $args = func_get_args();
        self::_checkArgs($args);
        $feed = $args[0];
        
        /* give the strategy a chance to do some initial processing */
        $this->_preExecute($feed);
        
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom = $ioc->get('org_tubepress_api_options_OptionsManager');
        
        $results = array();
        $index   = 0;
        $total   = $this->_countVideosInFeed($feed);
        
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Now building %d video(s) from raw feed', $total);
        
        for ($index = 0; $index < $total; $index++) {
            
            if (!$this->_canHandleVideo($index)) {
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Skipping video at index %d', $index);
                continue;
            }

            $results[] = $this->_buildVideo($index);
        }

        /* give the strategy a chance to do some post processing */
        $this->_postExecute($feed);
        
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Built %d video(s) from raw feed', sizeof($results));

        return $results;
    }
    
    /**
     * Safely attempts to unserialize serialized PHP.
     * 
     * @param unknown_type $raw The item to attempt to unserialize.
     * @throws Exception If the item cannot be unserialized.
     * 
     * @return unknown_type The unserialized PHP.
     */
    protected static function _unserializePhpFeed($raw)
    {
        $result = false;
        
        if (is_string($raw) && trim($raw) != '' && preg_match("/^(i|s|a|o|d):(.*);/si",$raw) > 0) { 
            $result = unserialize($raw);
        }
            
        if ($result === false) {
            throw new Exception(sprintf('Unable to unserialize PHP from feed'));
        }
        
        return $result;
    }
    
    protected abstract function _preExecute($feed);
    protected abstract function _postExecute($feed);
    protected abstract function _countVideosInFeed($feed);
    protected abstract function _canHandleVideo($index);
    protected abstract function _getAuthorDisplayName($index);
    protected abstract function _getAuthorUid($index);
    protected abstract function _getCategory($index);
    protected abstract function _getRawCommentCount($index);
    protected abstract function _getDescription($index);
    protected abstract function _getDurationInSeconds($index);
    protected abstract function _getHomeUrl($index);
    protected abstract function _getId($index);
    protected abstract function _getKeywordsArray($index);
    protected abstract function _getRawLikeCount($index);
    protected abstract function _getRatingAverage($index);
    protected abstract function _getRawRatingCount($index);
    protected abstract function _getThumbnailUrlsArray($index);
    protected abstract function _getTimeLastUpdatedInUnixTime($index);
    protected abstract function _getTimePublishedInUnixTime($index);
    protected abstract function _getTitle($index);
    protected abstract function _getRawViewCount($index);  
    
    protected static function _checkArgs($args)
    {
        if (sizeof($args) !== 1) {
            throw new Exception("Expected one arg. You sent %d", sizeof($args));
        }
    }
    
    protected function _pickThumbnailUrl($urls)
    {
        if (!is_array($urls) || sizeof($urls) == 0) {
            return '';
        }

        $random = $this->_tpom->get(org_tubepress_api_const_options_names_Display::RANDOM_THUMBS);
        if ($random) {
            return $urls[array_rand($urls)];
        } else {
            return $urls[0];
        }
    }
    
    private function _buildVideo($index)
    {
        /* collect the pieces of the video */
        $authorDisplayName = $this->_getAuthorDisplayName($index);
        $authorUid         = $this->_getAuthorUid($index);
        $category          = $this->_getCategory($index);
        $commentCount      = self::_fancyNumber($this->_getRawCommentCount($index));
        $description       = $this->_trimDescription($this->_getDescription($index));
        $duration          = org_tubepress_impl_util_TimeUtils::secondsToHumanTime($this->_getDurationInSeconds($index));
        $homeUrl           = $this->_getHomeUrl($index);
        $id                = $this->_getId($index);
        $keywordsArray     = $this->_getKeywordsArray($index);
        $likesCount        = self::_fancyNumber($this->_getRawLikeCount($index));
        $ratingAverage     = $this->_getRatingAverage($index);
        $ratingCount       = self::_fancyNumber($this->_getRawRatingCount($index));
        $thumbUrl          = $this->_pickThumbnailUrl($this->_getThumbnailUrlsArray($index));
        $timeLastUpdated   = $this->_fancyTime($this->_getTimeLastUpdatedInUnixTime($index));
        $timePublished     = $this->_fancyTime($this->_getTimePublishedInUnixTime($index));
        $title             = $this->_getTitle($index);
        $viewCount         = self::_fancyNumber($this->_getRawViewCount($index));

        /* now build a video out of them */
        $vid = new org_tubepress_api_video_Video();
        
        $vid->setAuthorDisplayName($authorDisplayName);
        $vid->setAuthorUid($authorUid);
        $vid->setCategory($category);
        $vid->setCommentCount($commentCount);
        $vid->setDescription($description);
        $vid->setDuration($duration);
        $vid->setHomeUrl($homeUrl);
        $vid->setId($id);
        $vid->setKeywords($keywordsArray);
        $vid->setLikesCount($likesCount);
        $vid->setRatingAverage($ratingAverage);
        $vid->setRatingCount($ratingCount);
        $vid->setThumbnailUrl($thumbUrl);
        $vid->setTimeLastUpdated($timeLastUpdated);
        $vid->setTimePublished($timePublished);
        $vid->setTitle($title);
        $vid->setViewCount($viewCount);
        
        return $vid;
    }
    
    private static function _fancyNumber($num)
    {
        if (!is_numeric($num)) {
            return 'N/A';
        }
        return number_format($num);
    }

    private function _fancyTime($unixTime)
    {
        if ($unixTime == '') {
            return '';
        }
        
        if ($this->_tpom->get(org_tubepress_api_const_options_names_Display::RELATIVE_DATES)) {
            return org_tubepress_impl_util_TimeUtils::getRelativeTime($unixTime);
        }
        return date($this->_tpom->get(org_tubepress_api_const_options_names_Advanced::DATEFORMAT), $unixTime);
    }
    
    private function _trimDescription($description)
    {
        $limit = $this->_tpom->get(org_tubepress_api_const_options_names_Display::DESC_LIMIT);

        if ($limit > 0 && strlen($description) > $limit) {
            $description = substr($description, 0, $limit) . '...';
        }
        return $description;
    }
}
