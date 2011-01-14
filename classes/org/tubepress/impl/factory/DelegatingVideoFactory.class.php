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
tubepress_load_classes(array('org_tubepress_api_factory_VideoFactory',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_provider_Provider'));

/**
 * Video factory that sends the feed to the right video factory based on the provider
 */
class org_tubepress_impl_factory_DelegatingVideoFactory implements org_tubepress_api_factory_VideoFactory
{
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
        $fact = self::_getDelegate();
        return $fact->feedToVideoArray($feed, $limit);
    }
    
    public function convertSingleVideo($feed)
    {
        $fact = self::_getDelegate();
        return $fact->convertSingleVideo($feed);
    }
    
    private static function _getDelegate()
    {
        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc       = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $provider = $pc->calculateCurrentVideoProvider();
        
        if ($provider === org_tubepress_api_provider_Provider::VIMEO) {
            return $ioc->get('org_tubepress_impl_factory_VimeoVideoFactory');
        }
        return $ioc->get('org_tubepress_impl_factory_YouTubeVideoFactory');    
    }
}
