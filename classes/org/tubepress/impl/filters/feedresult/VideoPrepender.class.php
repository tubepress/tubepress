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
tubepress_load_classes(array('org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_const_filters_ExecutionPoint'));

/**
 * Appends/moves a video the front of the gallery based on the query string parameter.
 */
class org_tubepress_impl_filters_feedresult_VideoPrepender
{
    const LOG_PREFIX = 'Video Prepender';
    
    public function filter()
    {
        $args       = func_get_args();
        $feedResult = $args[0];
        
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $qss = $ioc->get('org_tubepress_api_querystring_QueryStringService');

        $customVideoId = $qss->getCustomVideo($_GET);

        /* they didn't set a custom video id */
        if ($customVideoId == '') {
            return $feedResult;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Prepending video %s to the gallery', $customVideoId);
        
        return self::_prependVideo($ioc, $customVideoId, $feedResult);
    }

    private static function _moveVideoUpFront($videos, $id)
    {
        for ($x = 0; $x < count($videos); $x++) {
            if ($videos[$x]->getId() == $id) {
                $saved = $videos[$x];
                unset($videos[$x]);
                array_unshift($videos, $saved);
                break;
            }
        }
        return $videos;
    }
    
    private static function _videoArrayAlreadyHasVideo($videos, $id)
    {
        foreach ($videos as $video) {
            if ($video->getId() == $id) {
                return true;
            }
        }
        return false;
    }
    
    private static function _prependVideo($ioc, $id, $feedResult)
    {
        $videos = $feedResult->getVideoArray();

        if (!is_array($videos)) {
            //log
            return $feedResult;
        }
        
        /* see if the array already has it */
        if (self::_videoArrayAlreadyHasVideo($videos, $id)) {
            $videos = self::_moveVideoUpFront($videos, $id);
            $feedResult->setVideoArray($videos);
            return $feedResult;
        }
    
        $provider = $ioc->get('org_tubepress_video_feed_provider_Provider');
        try {
            $video = $provider->getSingleVideo($customVideoId);
            array_unshift($videos, $video);
        } catch (Exception $e) {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Could not prepend video %s to the gallery: %s', $customVideoId, $e->getMessage());
        }
        
        /* modify the feed result */
        $feedResult->setVideoArray($videos);
        
        return $feedResult;
    }
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_feedresult_VideoPrepender');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, array($instance, 'filter'));