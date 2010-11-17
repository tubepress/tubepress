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
tubepress_load_classes(array('org_tubepress_api_feed_UrlBuilder',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_ioc_IocDelegateUtils',
    'org_tubepress_api_feed_UrlBuilder'));

/**
 * Builds URLs based on the urrent provider
 *
 */
class org_tubepress_url_impl_DelegatingUrlBuilder implements org_tubepress_api_feed_UrlBuilder
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_video_feed_provider_Provider::VIMEO => 'org_tubepress_url_impl_VimeoUrlBuilder',
    );
    
    private static $_defaultDelegateName = 'org_tubepress_url_impl_YouTubeUrlBuilder';
    
    /**
     * Builds a URL for a list of videos
     *
     * @return string The request URL for this gallery
     */
    public function buildGalleryUrl($currentPage)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(
            self::$_providerToBeanNameMap, 
            self::$_defaultDelegateName)->buildGalleryUrl($currentPage);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     */
    public function buildSingleVideoUrl($id)
    {   
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(
            self::$_providerToBeanNameMap,
            self::$_defaultDelegateName)->buildSingleVideoUrl($id);
    }
}
