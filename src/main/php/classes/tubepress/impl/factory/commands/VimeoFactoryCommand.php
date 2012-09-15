<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
 * Video factory for Vimeo
 */
class tubepress_impl_factory_commands_VimeoFactoryCommand extends tubepress_impl_factory_commands_AbstractFactoryCommand
{
    private $_videoArray;

    private $_unserialized;

    private $_logger;

    /**
     * Determine if we can handle this feed.
     *
     * @param mixed $feed The feed to handle.
     *
     * @return boolean True if this command can handle the feed, false otherwise.
     */
    protected final function _canHandleFeed($feed)
    {
        $this->_unserialized = @unserialize($feed);

        return $this->_unserialized !== false && $this->_unserialized->stat === 'ok';
    }

    /**
     * Perform pre-construction activites for the feed.
     *
     * @param mixed $feed The feed to construct.
     *
     * @return void
     */
    protected final function _preExecute($feed)
    {
        $unserialized = $this->_unserialized;

        if (isset($unserialized->video)) {

            $this->_videoArray = (array) $unserialized->video;

            unset($this->_unserialized);

            return;
        }

        if (isset($unserialized->videos) && isset($unserialized->videos->video)) {

            $this->_videoArray = (array) $unserialized->videos->video;

            unset($this->_unserialized);

            return;
        }

        unset($this->_unserialized);

        $this->_videoArray = array();
    }

    /**
     * Perform post-construction activites for the feed.
     *
     * @param mixed $feed The feed we used.
     *
     * @return void
     */
    protected final function _postExecute($feed)
    {
        unset($this->_videoArray);
    }

    /**
     * Count the number of videos that we think are in this feed.
     *
     * @param mixed $feed The feed.
     *
     * @return integer An estimated count of videos in this feed.
     */
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
        return @strtotime($this->_videoArray[$index]->upload_date);
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
        return $this->_videoArray[$index]->embed_privacy !== 'nowhere';
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

    /**
     * @return ehough_epilog_api_ILogger Get the logger for this command.
     */
    protected function getLogger()
    {
       if (! isset($this->_logger)) {

           $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Vimeo Video Factory');
       }

       return $this->_logger;
    }
}
