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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
));

/**
 * Appends/moves a video the front of the gallery based on the query string parameter.
 */
class org_tubepress_impl_plugin_filters_providerresult_VideoPrepender
{
    const LOG_PREFIX = 'Video Prepender';

    public function alter_providerResult(org_tubepress_api_provider_ProviderResult $providerResult)
    {
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);

        $customVideoId = $hrps->getParamValue(org_tubepress_api_const_http_ParamName::VIDEO);

        /* they didn't set a custom video id */
        if ($customVideoId == '') {

            return $providerResult;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Prepending video %s to the gallery', $customVideoId);

        return self::_prependVideo($ioc, $customVideoId, $providerResult);
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

    private static function _prependVideo($ioc, $id, $providerResult)
    {
        $videos = $providerResult->getVideoArray();

        /* see if the array already has it */
        if (self::_videoArrayAlreadyHasVideo($videos, $id)) {
            $videos = self::_moveVideoUpFront($videos, $id);
            $providerResult->setVideoArray($videos);
            return $providerResult;
        }

        $provider = $ioc->get(org_tubepress_api_provider_Provider::_);
        try {
            $video = $provider->getSingleVideo($id);
            array_unshift($videos, $video);
        } catch (Exception $e) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Could not prepend video %s to the gallery: %s', $customVideoId, $e->getMessage());
        }

        /* modify the feed result */
        $providerResult->setVideoArray($videos);

        return $providerResult;
    }
}
