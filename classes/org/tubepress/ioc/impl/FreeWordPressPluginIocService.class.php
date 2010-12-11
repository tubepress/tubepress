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

function_exists('tubepress_load_classes') || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_ioc_impl_TubePressIocService',
    'org_tubepress_api_provider_Provider'));

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_ioc_impl_FreeWordPressPluginIocService extends org_tubepress_ioc_impl_TubePressIocService
{
    /**
     * Default constructor.
     */
    function __construct()
    {
        parent::__construct();
        
        $this->bind('org_tubepress_api_cache_Cache')                         ->to('org_tubepress_impl_cache_PearCacheLiteCacheService');
        
        $this->bind('org_tubepress_api_embedded_EmbeddedPlayer')             ->to('org_tubepress_impl_embedded_DelegatingEmbeddedPlayer');
        $this->bind('org_tubepress_api_embedded_EmbeddedPlayer')             ->labeled(org_tubepress_api_provider_Provider::YOUTUBE)
                                                                                ->to('org_tubepress_impl_embedded_YouTubeEmbeddedPlayer');
        $this->bind('org_tubepress_api_embedded_EmbeddedPlayer')             ->labeled(org_tubepress_api_provider_Provider::VIMEO)
                                                                                ->to('org_tubepress_impl_embedded_VimeoEmbeddedPlayer');
        
        $this->bind('org_tubepress_api_http_AgentDetector')                     ->to('org_tubepress_impl_http_MobileEspBrowserDetector');
        $this->bind('org_tubepress_api_gallery_Gallery')                            ->to('org_tubepress_impl_gallery_SimpleGallery');
        $this->bind('org_tubepress_api_message_MessageService')                     ->to('org_tubepress_impl_message_WordPressMessageService');
        $this->bind('org_tubepress_api_options_OptionsManager')             ->to('org_tubepress_options_manager_SimpleOptionsManager');    
        $this->bind('org_tubepress_api_options_StorageManager')             ->to('org_tubepress_options_storage_WordPressStorageManager');
        $this->bind('org_tubepress_api_options_OptionValidator')  ->to('org_tubepress_options_validation_SimpleInputValidationService');    
        $this->bind('org_tubepress_api_pagination_Pagination')               ->to('org_tubepress_pagination_DiggStylePaginationService');
        $this->bind('org_tubepress_api_player_Player')                              ->to('org_tubepress_player_SimplePlayer');
        $this->bind('org_tubepress_api_querystring_QueryStringService')             ->to('org_tubepress_querystring_SimpleQueryStringService');
        $this->bind('org_tubepress_api_shortcode_ShortcodeParser')                  ->to('org_tubepress_shortcode_SimpleShortcodeParser');
        $this->bind('org_tubepress_api_single_SingleVideo')                         ->to('org_tubepress_single_SimpleSingleVideo');
        $this->bind('org_tubepress_api_theme_ThemeHandler')                         ->to('org_tubepress_theme_SimpleThemeHandler');
        
        /* URL building */
        $this->bind('org_tubepress_api_feed_UrlBuilder')                             ->to('org_tubepress_url_impl_DelegatingUrlBuilder');
        $this->bind('org_tubepress_api_feed_UrlBuilder')                             ->labeled(org_tubepress_api_provider_Provider::YOUTUBE)
                                                                                ->to('org_tubepress_url_impl_YouTubeUrlBuilder');
        $this->bind('org_tubepress_api_feed_UrlBuilder')                             ->labeled(org_tubepress_api_provider_Provider::VIMEO)
                                                                                ->to('org_tubepress_url_impl_VimeoUrlBuilder');
        
        /* Video factories */
        $this->bind('org_tubepress_api_feed_VideoFactory')                 ->to('org_tubepress_video_factory_DelegatingVideoFactory');
        $this->bind('org_tubepress_api_feed_VideoFactory')                 ->labeled(org_tubepress_api_provider_Provider::YOUTUBE)
                                                                                ->to('org_tubepress_video_factory_impl_YouTubeVideoFactory');
        $this->bind('org_tubepress_api_feed_VideoFactory')                 ->labeled(org_tubepress_api_provider_Provider::VIMEO)
                                                                                ->to('org_tubepress_video_factory_impl_VimeoVideoFactory');
        
        /* Feed inspection */
        $this->bind('org_tubepress_api_feed_FeedInspector')->to('org_tubepress_video_feed_inspection_DelegatingFeedInspectionService');
        $this->bind('org_tubepress_api_feed_FeedInspector')->labeled(org_tubepress_api_provider_Provider::YOUTUBE)
                                                                                ->to('org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService');
        $this->bind('org_tubepress_api_feed_FeedInspector')->labeled(org_tubepress_api_provider_Provider::VIMEO)
                                                                                ->to('org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService');
        /* Video provider */
        $this->bind('org_tubepress_api_provider_Provider')               ->to('org_tubepress_video_feed_provider_SimpleProvider');
        $this->bind('org_tubepress_api_feed_FeedFetcher')  ->to('org_tubepress_video_feed_retrieval_HTTPRequest2');
    }
}
