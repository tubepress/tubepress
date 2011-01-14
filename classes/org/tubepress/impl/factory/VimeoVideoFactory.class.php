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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_factory_VideoFactory',
    'org_tubepress_api_video_Video',
    'org_tubepress_api_const_options_Display',
    'org_tubepress_impl_util_TimeUtils'));

/**
 * Video factory for Vimeo
 */
class org_tubepress_impl_factory_VimeoVideoFactory implements org_tubepress_api_factory_VideoFactory
{
    const LOG_PREFIX = 'Vimeo Video Factory';

    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param unknown                      $rawFeed The raw feed result from the video provider
     * @param int                          $limit   The max number of videos to return
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function feedToVideoArray($rawFeed, $limit)
    {
        $feed = self::unserialize($rawFeed);
        
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Now parsing video(s)');

        $entries = $feed->videos->video;
        
        return $this->_buildVideos($entries);
    }

    /**
     * Converts a single raw video into a TubePress video
     *
     * @param unknown $rawFeed The raw feed result from the video provider
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function convertSingleVideo($rawFeed)
    {
        $feed = self::unserialize($rawFeed);
        
        return $this->_buildVideos($feed->video);
    }

    private function _buildVideos($entries)
    {
        $results   = array();
        $index     = 0;
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom      = $ioc->get('org_tubepress_api_options_OptionsManager');

        if (is_array($entries) && sizeof($entries) > 0) {
            foreach ($entries as $entry) {

                if ($index > 0 && $index++ >= $limit) {
                    org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Reached limit of %d videos', $limit);
                    break;
                }

                $results[] = $this->_createVideo($entry, $tpom);
            }
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Built %d video(s) from Vimeo\'s feed', sizeof($results));
        return $results;
    }

    /**
     * Creates a video from a single "entry" XML node
     *
     * @return org_tubepress_api_video_Video The org_tubepress_api_video_Video representation of this node
     */
    private function _createVideo($entry, org_tubepress_api_options_OptionsManager $tpom)
    {
        $vid = new org_tubepress_api_video_Video();

        $vid->setAuthorDisplayName($entry->owner->display_name);
        $vid->setAuthorUid($entry->owner->username);
        $vid->setDescription($this->_getDescription($entry, $tpom));
        $vid->setDuration(org_tubepress_impl_util_TimeUtils::secondsToHumanTime($entry->duration));
        $vid->setHomeUrl('http://vimeo.com/' . $entry->id);
        $vid->setId($entry->id);
        $vid->setThumbnailUrl($this->_getThumbnailUrl($entry));
        $vid->setTimePublished($this->_getTimePublished($entry, $tpom));
        $vid->setTitle($entry->title);
        $vid->setViewCount($this->_getViewCount($entry));
        $vid->setLikesCount($entry->number_of_likes);

        if (isset($entry->tags) && is_array($entry->tags->tag)) {
            $tags = array();

            foreach ($entry->tags->tag as $tag) {
                $tags[] = $tag->_content;
            }
            $vid->setKeywords($tags);
        } else {
            $vid->setKeywords(array());
        }
        return $vid;
    }

    protected function _getDescription($entry, org_tubepress_api_options_OptionsManager $tpom)
    {
        $limit = $tpom->get(org_tubepress_api_const_options_Display::DESC_LIMIT);
        $desc  = $entry->description;

        if ($limit > 0 && strlen($desc) > $limit) {
            $desc = substr($desc, 0, $limit) . '...';
        }
        return $desc;
    }

    protected function _getThumbnailUrl($entry)
    {
        return $entry->thumbnails->thumbnail[0]->_content;
    }

    private function _getTimePublished($entry, org_tubepress_api_options_OptionsManager $tpom)
    {
        $date    = $entry->upload_date;
        $seconds = strtotime($date);

        if ($tpom->get(org_tubepress_api_const_options_Display::RELATIVE_DATES)) {
            return org_tubepress_impl_util_TimeUtils::getRelativeTime($seconds);
        }
        return date($tpom->get(org_tubepress_api_const_options_Advanced::DATEFORMAT), $seconds);
    }

    private function _getViewCount($entry)
    {
        return number_format($entry->number_of_plays);
    }
    
    private static function unserialize($raw)
    {
        $result = unserialize($raw);
        
        if ($result === FALSE) {
            throw new Exception(sprintf('Unable to unserialize feed from Vimeo: %s', $raw));
        }
        
        return $result;
    }
}
