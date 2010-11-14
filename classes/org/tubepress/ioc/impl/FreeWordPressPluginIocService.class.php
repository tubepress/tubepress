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
tubepress_load_classes(array('org_tubepress_ioc_impl_PhpCraftyIocService',
    'org_tubepress_browser_BrowserDetector',                     'org_tubepress_browser_MobileEspBrowserDetector',
    'org_tubepress_cache_CacheService',                          'org_tubepress_cache_PearCacheLiteCacheService',
    'org_tubepress_embedded_EmbeddedPlayerService',              'org_tubepress_embedded_impl_DelegatingEmbeddedPlayerService',
    'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
    'org_tubepress_gallery_Gallery',                             'org_tubepress_gallery_SimpleGallery',
    'org_tubepress_message_MessageService',                      'org_tubepress_message_impl_WordPressMessageService',
    'org_tubepress_options_manager_OptionsManager',              'org_tubepress_options_manager_SimpleOptionsManager',    
    'org_tubepress_options_storage_StorageManager',              'org_tubepress_options_storage_WordPressStorageManager',
    'org_tubepress_options_validation_InputValidationService',   'org_tubepress_options_validation_SimpleInputValidationService',
    'org_tubepress_pagination_PaginationService',                'org_tubepress_pagination_DiggStylePaginationService',
    'org_tubepress_player_Player',                               'org_tubepress_player_SimplePlayer',
    'org_tubepress_querystring_QueryStringService',              'org_tubepress_querystring_SimpleQueryStringService',
    'org_tubepress_shortcode_ShortcodeParser',                   'org_tubepress_shortcode_SimpleShortcodeParser',
    'org_tubepress_single_SingleVideo',                          'org_tubepress_single_SimpleSingleVideo',
    'org_tubepress_theme_ThemeHandler',                          'org_tubepress_theme_SimpleThemeHandler',
    'org_tubepress_url_UrlBuilder',                              'org_tubepress_url_impl_DelegatingUrlBuilder',
    'org_tubepress_video_factory_VideoFactory',                  'org_tubepress_video_factory_DelegatingVideoFactory',
    'org_tubepress_video_feed_inspection_FeedInspectionService', 'org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
    'org_tubepress_video_feed_provider_Provider',                'org_tubepress_video_feed_provider_SimpleProvider',
    'org_tubepress_video_feed_retrieval_FeedRetrievalService',   'org_tubepress_video_feed_retrieval_HTTPRequest2',
    'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',    'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
    'org_tubepress_url_impl_VimeoUrlBuilder',
    'org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService',
    'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
    'org_tubepress_url_impl_YouTubeUrlBuilder',
    'org_tubepress_video_factory_impl_YouTubeVideoFactory',
    'org_tubepress_video_factory_impl_VimeoVideoFactory'

));

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_ioc_impl_FreeWordPressPluginIocService extends org_tubepress_ioc_impl_PhpCraftyIocService
{
    /**
     * Default constructor.
     */
    function __construct()
    {
        $implementationMap = array(
            'org_tubepress_browser_BrowserDetector'                     => 'org_tubepress_browser_MobileEspBrowserDetector',
            'org_tubepress_cache_CacheService'                          => 'org_tubepress_cache_PearCacheLiteCacheService',
            'org_tubepress_embedded_EmbeddedPlayerService'              => 'org_tubepress_embedded_impl_DelegatingEmbeddedPlayerService',
            'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService'  => 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
            'org_tubepress_gallery_Gallery'                             => 'org_tubepress_gallery_SimpleGallery',
            'org_tubepress_message_MessageService'                      => 'org_tubepress_message_impl_WordPressMessageService',
            'org_tubepress_options_manager_OptionsManager'              => 'org_tubepress_options_manager_SimpleOptionsManager',    
            'org_tubepress_options_storage_StorageManager'              => 'org_tubepress_options_storage_WordPressStorageManager',
            'org_tubepress_options_validation_InputValidationService'   => 'org_tubepress_options_validation_SimpleInputValidationService',    
            'org_tubepress_pagination_PaginationService'                => 'org_tubepress_pagination_DiggStylePaginationService',
            'org_tubepress_player_Player'                               => 'org_tubepress_player_SimplePlayer',
            'org_tubepress_querystring_QueryStringService'              => 'org_tubepress_querystring_SimpleQueryStringService',
            'org_tubepress_shortcode_ShortcodeParser'                   => 'org_tubepress_shortcode_SimpleShortcodeParser',
            'org_tubepress_single_SingleVideo'                          => 'org_tubepress_single_SimpleSingleVideo',
            'org_tubepress_theme_ThemeHandler'                          => 'org_tubepress_theme_SimpleThemeHandler',
            'org_tubepress_url_UrlBuilder'                              => 'org_tubepress_url_impl_DelegatingUrlBuilder',
            'org_tubepress_video_factory_VideoFactory'                  => 'org_tubepress_video_factory_DelegatingVideoFactory',
            'org_tubepress_video_feed_inspection_FeedInspectionService' => 'org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
            'org_tubepress_video_feed_provider_Provider'                => 'org_tubepress_video_feed_provider_SimpleProvider',
            'org_tubepress_video_feed_retrieval_FeedRetrievalService'   => 'org_tubepress_video_feed_retrieval_HTTPRequest2'
       );
        
        foreach ($implementationMap as $interface => $implementation) {
            $this->def($interface, $this->impl($implementation));
        }
    }
}
