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
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_ioc_IocDelegateUtils'));

/**
 * Builds URLs based on the current provider
 *
 */
class org_tubepress_url_DelegatingUrlBuilder
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_video_feed_provider_Provider::VIMEO     => org_tubepress_ioc_IocService::URL_BUILDER_VIMEO,
    );
    
    private static $_defaultDelegateName = org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE;
    
    /**
     * Builds a URL for a list of videos
     *
     * @return string The request URL for this gallery
     */
    public static function buildGalleryUrl(org_tubepress_ioc_IocService $ioc, $currentPage)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate($ioc, 
            self::$_providerToBeanNameMap, 
            self::$_defaultDelegateName)->buildGalleryUrl($ioc, $currentPage);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     */
    public static function buildSingleVideoUrl(org_tubepress_ioc_IocService $ioc, $id)
    {   
        return org_tubepress_ioc_IocDelegateUtils::getDelegate($ioc,
            self::$_providerToBeanNameMap,
            self::$_defaultDelegateName)->buildSingleVideoUrl($ioc, $id);
    }
}
