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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_provider_Provider',
    'org_tubepress_impl_ioc_TubePressIocService',
));

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

        $this->bind('org_tubepress_api_bootstrap_Bootstrapper')           ->to('org_tubepress_impl_bootstrap_TubePressBootstrapper');
        $this->bind('org_tubepress_api_cache_Cache')                      ->to('org_tubepress_impl_cache_PearCacheLiteCacheService');
        $this->bind('org_tubepress_api_embedded_EmbeddedHtmlGenerator')   ->to('org_tubepress_impl_embedded_EmbeddedPlayerChain');
        $this->bind('org_tubepress_api_environment_Detector')             ->to('org_tubepress_impl_environment_SimpleEnvironmentDetector');
        $this->bind('org_tubepress_api_exec_ExecutionContext')            ->to('org_tubepress_impl_exec_MemoryExecutionContext');
        $this->bind('org_tubepress_api_factory_VideoFactory')             ->to('org_tubepress_impl_factory_VideoFactoryChain');
        $this->bind('org_tubepress_api_feed_FeedFetcher')                 ->to('org_tubepress_impl_feed_CacheAwareFeedFetcher');
        $this->bind('org_tubepress_api_feed_FeedInspector')               ->to('org_tubepress_impl_feed_FeedInspectorChain');
        $this->bind('org_tubepress_api_filesystem_Explorer')              ->to('org_tubepress_impl_filesystem_FsExplorer');
        $this->bind('org_tubepress_api_html_HeadHtmlGenerator')           ->to('org_tubepress_impl_html_DefaultHeadHtmlGenerator');
        $this->bind('org_tubepress_api_http_HttpClient')                  ->to('org_tubepress_impl_http_HttpClientChain');
        $this->bind('org_tubepress_spi_http_HttpContentDecoder')          ->to('org_tubepress_impl_http_HttpContentDecoderChain');
        $this->bind('org_tubepress_spi_http_HttpMessageParser')           ->to('org_tubepress_impl_http_DefaultHttpMessageParser');
        $this->bind('org_tubepress_api_http_HttpRequestParameterService') ->to('org_tubepress_impl_http_DefaultHttpRequestParameterService');
        $this->bind('org_tubepress_api_http_HttpResponseHandler')         ->to('org_tubepress_impl_http_HttpResponseHandlerChain');
        $this->bind('org_tubepress_spi_http_HttpTransferDecoder')         ->to('org_tubepress_impl_http_HttpTransferDecoderChain');
        $this->bind('org_tubepress_api_message_MessageService')           ->to('org_tubepress_impl_message_WordPressMessageService');
        $this->bind('org_tubepress_api_options_OptionDescriptorReference')->to('org_tubepress_impl_options_DefaultOptionDescriptorReference');
        $this->bind('org_tubepress_api_options_OptionValidator')          ->to('org_tubepress_impl_options_DefaultOptionValidator');
        $this->bind('org_tubepress_api_options_StorageManager')           ->to('org_tubepress_impl_options_WordPressStorageManager');
        $this->bind('org_tubepress_api_options_ui_FormHandler')           ->to('org_tubepress_impl_env_wordpress_WordPressFormHandler');
        $this->bind('org_tubepress_spi_options_ui_FieldBuilder')          ->to('org_tubepress_impl_options_ui_DefaultFieldBuilder');
        $this->bind('org_tubepress_api_plugin_PluginManager')             ->to('org_tubepress_impl_plugin_PluginManagerImpl');
        $this->bind('org_tubepress_spi_patterns_cor_Chain')               ->to('org_tubepress_impl_patterns_cor_ChainGang');
        $this->bind('org_tubepress_api_player_PlayerHtmlGenerator')       ->to('org_tubepress_impl_player_DefaultPlayerHtmlGenerator');
        $this->bind('org_tubepress_api_provider_Provider')                ->to('org_tubepress_impl_provider_SimpleProvider');
        $this->bind('org_tubepress_api_provider_ProviderCalculator')      ->to('org_tubepress_impl_provider_SimpleProviderCalculator');
        $this->bind('org_tubepress_api_querystring_QueryStringService')   ->to('org_tubepress_impl_querystring_SimpleQueryStringService');
        $this->bind('org_tubepress_api_shortcode_ShortcodeHtmlGenerator') ->to('org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain');
        $this->bind('org_tubepress_api_shortcode_ShortcodeParser')        ->to('org_tubepress_impl_shortcode_SimpleShortcodeParser');
        $this->bind('org_tubepress_api_template_Template')                ->to('org_tubepress_impl_template_SimpleTemplate');
        $this->bind('org_tubepress_api_template_TemplateBuilder')         ->to('org_tubepress_impl_template_SimpleTemplateBuilder');
        $this->bind('org_tubepress_api_theme_ThemeHandler')               ->to('org_tubepress_impl_theme_SimpleThemeHandler');
        $this->bind('org_tubepress_api_feed_UrlBuilder')                  ->to('org_tubepress_impl_feed_UrlBuilderChain');
    }
}
