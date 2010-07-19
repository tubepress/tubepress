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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_ioc_PhpCraftyIocService',
    'org_tubepress_ioc_IocService',
    'org_tubepress_cache_SimpleCacheService',
    'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
    'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
    'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
    'org_tubepress_message_WordPressMessageService',
    'org_tubepress_options_manager_SimpleOptionsManager',
    'org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_pagination_DiggStylePaginationService',
    'org_tubepress_url_impl_YouTubeUrlBuilder',
    'org_tubepress_url_impl_VimeoUrlBuilder',
    'org_tubepress_video_factory_impl_YouTubeVideoFactory',
    'org_tubepress_video_factory_impl_VimeoVideoFactory',
    'org_tubepress_video_factory_impl_LocalVideoFactory',
    'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
    'org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService',
    'org_tubepress_video_feed_inspection_impl_LocalFeedInspectionService',
    'org_tubepress_video_feed_retrieval_HTTPRequest2'));

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_ioc_DefaultIocService extends org_tubepress_ioc_PhpCraftyIocService implements org_tubepress_ioc_IocService
{
    /**
     * Default constructor.
     */
    function __construct()
    {
        $this->def(org_tubepress_ioc_IocService::CACHE_SERVICE,
            $this->impl('org_tubepress_cache_SimpleCacheService'));

            
        $this->def(org_tubepress_ioc_IocService::EMBEDDED_IMPL_YOUTUBE,
            $this->impl('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService'));
            
        $this->def(org_tubepress_ioc_IocService::EMBEDDED_IMPL_VIMEO,
            $this->impl('org_tubepress_embedded_impl_VimeoEmbeddedPlayerService'));
                
        $this->def(org_tubepress_ioc_IocService::EMBEDDED_IMPL_LONGTAIL,
            $this->impl('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService'));
            
            
        $this->def(org_tubepress_ioc_IocService::MESSAGE_SERVICE, 
            $this->impl('org_tubepress_message_WordPressMessageService'));
            
            
        $this->def(org_tubepress_ioc_IocService::OPTIONS_MANAGER,
            $this->impl('org_tubepress_options_manager_SimpleOptionsManager'));    
        
        $this->def(org_tubepress_ioc_IocService::OPTIONS_STORAGE_MANAGER,
            $this->impl('org_tubepress_options_storage_WordPressStorageManager'));    
            
            
        $this->def(org_tubepress_ioc_IocService::PAGINATION_SERVICE,
            $this->impl('org_tubepress_pagination_DiggStylePaginationService'));    
            

        //single video


        $this->def(org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE,
            $this->impl('org_tubepress_url_impl_YouTubeUrlBuilder'));
            
        $this->def(org_tubepress_ioc_IocService::URL_BUILDER_VIMEO,
            $this->impl('org_tubepress_url_impl_VimeoUrlBuilder'));
                

        $this->def(org_tubepress_ioc_IocService::VIDEO_FACTORY_YOUTUBE,
            $this->impl('org_tubepress_video_factory_impl_YouTubeVideoFactory'));
            
        $this->def(org_tubepress_ioc_IocService::VIDEO_FACTORY_VIMEO,
            $this->impl('org_tubepress_video_factory_impl_VimeoVideoFactory'));
            
        $this->def(org_tubepress_ioc_IocService::VIDEO_FACTORY_LOCAL,
            $this->impl('org_tubepress_video_factory_impl_LocalVideoFactory'));

            
        $this->def(org_tubepress_ioc_IocService::FEED_INSPECTION_YOUTUBE, 
            $this->impl('org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService'));
            
        $this->def(org_tubepress_ioc_IocService::FEED_INSPECTION_VIMEO, 
            $this->impl('org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService'));

        $this->def(org_tubepress_ioc_IocService::FEED_INSPECTION_LOCAL,
            $this->impl('org_tubepress_video_feed_inspection_impl_LocalFeedInspectionService'));

            
        $this->def(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE,
            $this->impl('org_tubepress_video_feed_retrieval_HTTPRequest2'));
    }
}
