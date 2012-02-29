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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_impl_factory_commands_AbstractFactoryCommand'
));

/**
 * Video factory for Vimeo
 */
class org_tubepress_impl_factory_commands_VimeoFactoryCommand extends org_tubepress_impl_factory_commands_AbstractFactoryCommand
{
    const LOG_PREFIX = 'Vimeo Factory Command';

    protected $_videoArray;

    protected function _canHandleFeed($feed)
    {
        try {

            $unserialized = org_tubepress_impl_factory_commands_AbstractFactoryCommand::_unserializePhpFeed($feed);

        } catch (Exception $e) {
            return false;
        }

        return $unserialized->stat === 'ok';
    }

    protected function _preExecute($feed)
    {
        $unserialized = org_tubepress_impl_factory_commands_AbstractFactoryCommand::_unserializePhpFeed($feed);

        if (isset($unserialized->video)) {
            $this->_videoArray = (array) $unserialized->video;
            return;
        }

        if (isset($unserialized->videos) && isset($unserialized->videos->video)) {
            $this->_videoArray = (array) $unserialized->videos->video;
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
}
