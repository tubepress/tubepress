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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_feed_FeedInspector',
    'org_tubepress_options_manager_OptionsManager'));

/**
 * Sends the feed to the right inspection service based on the provider.
 */
class org_tubepress_video_feed_inspection_DelegatingFeedInspectionService
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_api_provider_Provider::VIMEO => 'org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService'
    );

    private static $_defaultDelegateBeanName = 'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService';

    public static function getTotalResultCount($rawFeed)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(self::$_providerToBeanNameMap,
           self::$_defaultDelegateBeanName)->getTotalResultCount($rawFeed);
    }

    public static function getQueryResultCount($rawFeed)
    {
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(self::$_providerToBeanNameMap,
            self::$_defaultDelegateBeanName)->getQueryResultCount($rawFeed);
    }
}
