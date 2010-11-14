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
tubepress_load_classes(array('org_tubepress_video_factory_VideoFactory',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_ioc_IocDelegateUtils',
    'org_tubepress_video_factory_impl_VimeoVideoFactory'));

/**
 * Video factory that sends the feed to the right video factory based on the provider
 */
class org_tubepress_video_factory_DelegatingVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_video_feed_provider_Provider::VIMEO => 'org_tubepress_video_factory_impl_VimeoVideoFactory'
    );
    
    private static $_defaultDelegateName = 'org_tubepress_video_factory_impl_YouTubeVideoFactory';
    
    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param unknown    $rss   The raw feed result from the video provider
     * @param int        $limit The max number of videos to return
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function feedToVideoArray($feed, $limit)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(self::$_providerToBeanNameMap, 
            self::$_defaultDelegateName)->feedToVideoArray($feed, $limit);
    }
    
    public function convertSingleVideo($feed)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(self::$_providerToBeanNameMap, 
            self::$_defaultDelegateName)->convertSingleVideo($feed);
    }
    

}
