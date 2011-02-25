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

function_exists('tubepress_load_classes') || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_impl_ioc_TubePressIocService',
    'org_tubepress_api_provider_Provider'));

/**
 * Dependency injector for TubePress in a WordPress environment
 */
class org_tubepress_impl_ioc_FreeWordPressPluginIocService extends org_tubepress_impl_ioc_TubePressIocService
{
    /**
     * Default constructor.
     */
    function __construct()
    {
        parent::__construct();
        
        $this->bind('org_tubepress_api_bootstrap_Bootstrapper')        ->to('org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapper');
        $this->bind('org_tubepress_api_cache_Cache')                   ->to('org_tubepress_impl_cache_PearCacheLiteCacheService');
        $this->bind('org_tubepress_api_embedded_EmbeddedPlayer')       ->to('org_tubepress_impl_embedded_DelegatingEmbeddedPlayer');
        $this->bind('org_tubepress_api_environment_Detector')          ->to('org_tubepress_impl_environment_SimpleEnvironmentDetector');
        $this->bind('org_tubepress_api_factory_VideoFactory')          ->to('org_tubepress_impl_factory_DelegatingVideoFactory');
        $this->bind('org_tubepress_api_feed_FeedFetcher')              ->to('org_tubepress_impl_feed_CacheAwareFeedFetcher');
        $this->bind('org_tubepress_api_feed_FeedInspector')            ->to('org_tubepress_impl_feed_DelegatingFeedInspector');
        $this->bind('org_tubepress_api_filesystem_Explorer')           ->to('org_tubepress_impl_filesystem_FsExplorer');
        $this->bind('org_tubepress_api_html_HtmlGenerator')              ->to('org_tubepress_impl_html_DefaultHtmlGenerator');
        $this->bind('org_tubepress_api_http_HttpClient')               ->to('org_tubepress_impl_http_FastHttpClient');
        $this->bind('org_tubepress_api_message_MessageService')        ->to('org_tubepress_impl_message_WordPressMessageService');
        $this->bind('org_tubepress_api_options_OptionsManager')        ->to('org_tubepress_impl_options_SimpleOptionsManager');    
        $this->bind('org_tubepress_api_options_OptionValidator')       ->to('org_tubepress_impl_options_SimpleOptionValidator');    
        $this->bind('org_tubepress_api_options_StorageManager')        ->to('org_tubepress_impl_options_WordPressStorageManager');
        $this->bind('org_tubepress_api_pagination_Pagination')         ->to('org_tubepress_impl_pagination_DiggStylePaginationService');
        $this->bind('org_tubepress_api_patterns_FilterManager')        ->to('org_tubepress_impl_patterns_FilterManagerImpl');
        $this->bind('org_tubepress_api_patterns_StrategyManager')      ->to('org_tubepress_impl_patterns_StrategyManagerImpl');
        $this->bind('org_tubepress_api_player_Player')                 ->to('org_tubepress_impl_player_SimplePlayer');
        $this->bind('org_tubepress_api_provider_Provider')             ->to('org_tubepress_impl_provider_SimpleProvider');
        $this->bind('org_tubepress_api_provider_ProviderCalculator')   ->to('org_tubepress_impl_provider_SimpleProviderCalculator');
        $this->bind('org_tubepress_api_querystring_QueryStringService')->to('org_tubepress_impl_querystring_SimpleQueryStringService');
        $this->bind('org_tubepress_api_shortcode_ShortcodeParser')     ->to('org_tubepress_impl_shortcode_SimpleShortcodeParser');
        $this->bind('org_tubepress_api_single_SingleVideo')            ->to('org_tubepress_impl_single_SimpleSingleVideo');
        $this->bind('org_tubepress_api_theme_ThemeHandler')            ->to('org_tubepress_impl_theme_SimpleThemeHandler');
        $this->bind('org_tubepress_api_url_UrlBuilder')                ->to('org_tubepress_impl_url_DelegatingUrlBuilder');
    }
}
