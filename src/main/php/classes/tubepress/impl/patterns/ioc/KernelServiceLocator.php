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

/**
 * A service locator for kernel services.
 */
class tubepress_impl_patterns_ioc_KernelServiceLocator
{
    /**
     * @var mixed This is a special member that is a reference to the core IOC service.
     *            It lets us perform lazy lookups for core services.
     */
    private static $_coreIocContainer;

    /**
     * @var tubepress_spi_http_AjaxHandler
     */
    private static $_ajaxHandler;

    /**
     * @var tubepress_spi_bootstrap_Bootstrapper
     */
    private static $_bootStrapper;

    /**
     * @var ehough_stash_api_Cache
     */
    private static $_cacheService;

    /**
     * @var tubepress_spi_embedded_EmbeddedHtmlGenerator
     */
    private static $_embeddedHtmlGenerator;

    /**
     * @var tubepress_spi_environment_EnvironmentDetector
     */
    private static $_environmentDetector;

    /**
     * @var ehough_tickertape_api_IEventDispatcher
     */
    private static $_eventDispatcher;

    /**
     * @var tubepress_spi_context_ExecutionContext
     */
    private static $_executionContext;

    /**
     * tubepress_spi_feed_FeedFetcher
     */
    private static $_feedFetcher;

    /**
     * tubepress_spi_feed_FeedInspector
     */
    private static $_feedInspector;

    /**
     * @var ehough_fimble_api_FileSystem
     */
    private static $_fileSystem;

    /**
     * @var ehough_fimble_api_FinderFactory
     */
    private static $_fileSystemFinderFactory;

    /**
     * @var tubepress_spi_html_HeadHtmlGenerator
     */
    private static $_headHtmlGenerator;

    /**
     * @var ehough_shortstop_api_HttpClient
     */
    private static $_httpClient;

    /**
     * @var ehough_shortstop_api_HttpResponseHandler
     */
    private static $_httpResponseHandler;

    /**
     * @var tubepress_spi_http_HttpRequestParameterService
     */
    private static $_httpRequestParameterService;

    /**
     * @var tubepress_spi_message_MessageService
     */
    private static $_messageService;

    /**
     * @var tubepress_spi_options_ui_FieldBuilder
     */
    private static $_optionsUiFieldBuilder;

    /**
     * @var tubepress_spi_options_ui_FormHandler
     */
    private static $_optionsUiFormHandler;

    /**
     * @var tubepress_api_service_options_OptionDescriptorReference
     */
    private static $_optionDescriptorReference;

    /**
     * @var tubepress_spi_options_StorageManager
     */
    private static $_optionStorageManager;

    /**
     * @var tubepress_spi_options_OptionValidator
     */
    private static $_optionValidator;

    /**
     * @var tubepress_spi_player_PlayerHtmlGenerator
     */
    private static $_playerHtmlGenerator;

    /**
     * @var tubepress_spi_plugin_PluginDiscoverer
     */
    private static $_pluginDiscoverer;

    /**
     * @var tubepress_spi_plugin_PluginRegistry
     */
    private static $_pluginRegistry;

    /**
     * @var tubepress_spi_querystring_QueryStringService
     */
    private static $_queryStringService;

    /**
     * @var tubepress_spi_shortcode_ShortcodeHtmlGenerator
     */
    private static $_shortcodeHtmlGenerator;

    /**
     * @var tubepress_spi_shortcode_ShortcodeParser
     */
    private static $_shortcodeParser;

    /**
     * @var ehough_contemplate_api_TemplateBuilder
     */
    private static $_templateBuilder;

    /**
     * @var tubepress_spi_theme_ThemeHandler
     */
    private static $_themeHandler;

    /**
     * @var tubepress_spi_feed_UrlBuilder
     */
    private static $_urlBuilder;

    /**
     * @var tubepress_spi_factory_VideoFactory
     */
    private static $_videoFactory;

    /**
     * @var tubepress_spi_provider_Provider
     */
    private static $_videoProvider;

    /**
     * @var tubepress_spi_provider_ProviderCalculator
     */
    private static $_videoProviderCalculator;

    /**
     * @return tubepress_spi_http_AjaxHandler
     */
    public static function getAjaxHandler()
    {
        return self::_lazyGet('_ajaxHandler', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_AJAX_HANDLER);
    }

    /**
     * @return tubepress_spi_bootstrap_Bootstrapper The bootstrapper.
     */
    public static function getBootstrapper()
    {
        return self::_lazyGet('_bootStrapper', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_BOOTSTRAPPER);
    }

    /**
     * @return ehough_stash_api_Cache The cache service.
     */
    public static function getCacheService()
    {
        return self::_lazyGet('_cacheService', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_CACHE);
    }

    /**
     * @return tubepress_spi_embedded_EmbeddedHtmlGenerator The embedded HTML generator.
     */
    public static function getEmbeddedHtmlGenerator()
    {
        return self::_lazyGet('_embeddedHtmlGenerator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EMBEDDED_HTML_GENERATOR);
    }

    /**
     * @return tubepress_spi_environment_EnvironmentDetector The environment detector.
     */
    public static function getEnvironmentDetector()
    {
        return self::_lazyGet('_environmentDetector', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_ENVIRONMENT_DETECTOR);
    }

    /**
     * @return ehough_tickertape_api_IEventDispatcher The event dispatcher.
     */
    public static function getEventDispatcher()
    {
        return self::_lazyGet('_eventDispatcher', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EVENT_DISPATCHER);
    }

    /**
     * @return tubepress_spi_context_ExecutionContext The execution context.
     */
    public static function getExecutionContext()
    {
        return self::_lazyGet('_executionContext', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EXECUTION_CONTEXT);
    }

    /**
     * @return tubepress_spi_feed_FeedFetcher The feed fetcher.
     */
    public static function getFeedFetcher()
    {
        return self::_lazyGet('_feedFetcher', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_FETCHER);
    }

    /**
     * @return tubepress_spi_feed_FeedInspector The feed inspector.
     */
    public static function getFeedInspector()
    {
        return self::_lazyGet('_feedInspector', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_INSPECTOR);
    }

    /**
     * @return ehough_fimble_api_FileSystem The filesystem service.
     */
    public static function getFileSystem()
    {
        return self::_lazyGet('_fileSystem', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM);
    }

    /**
     * @return ehough_fimble_api_FinderFactory The finder factory.
     */
    public static function getFileSystemFinderFactory()
    {
        return self::_lazyGet('_fileSystemFinderFactory', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM_FINDER_FACTORY);
    }

    /**
     * @return tubepress_spi_html_HeadHtmlGenerator The head HTML generator.
     */
    public static function getHeadHtmlGenerator()
    {
        return self::_lazyGet('_headHtmlGenerator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HEAD_HTML_GENERATOR);
    }

    /**
     * @return ehough_shortstop_api_HttpClient The HTTP client.
     */
    public static function getHttpClient()
    {
        return self::_lazyGet('_httpClient', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_CLIENT);
    }

    /**
     * @return ehough_shortstop_api_HttpResponseHandler The HTTP response handler.
     */
    public static function getHttpResponseHandler()
    {
        return self::_lazyGet('_httpResponseHandler', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_RESPONSE_HANDLER);
    }

    /**
     * @return tubepress_spi_http_HttpRequestParameterService The HTTP request parameter service.
     */
    public static function getHttpRequestParameterService()
    {
        return self::_lazyGet('_httpRequestParameterService', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_REQUEST_PARAMS);
    }

    /**
     * @return tubepress_spi_message_MessageService The message service.
     */
    public static function getMessageService()
    {
        /**
         * This service is not set by the core IOC container.
         */
        return self::$_messageService;
    }

    /**
     * @return tubepress_spi_options_ui_FieldBuilder The options UI field builder.
     */
    public static function getOptionsUiFieldBuilder()
    {
        return self::_lazyGet('_optionsUiFieldBuilder', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTIONS_UI_FIELDBUILDER);
    }

    /**
     * @return tubepress_spi_options_ui_FormHandler The UI form handler.
     */
    public static function getOptionsUiFormHandler()
    {
        /**
         * This service is not set by the core IOC container.
         */
        return self::$_optionsUiFormHandler;
    }

    /**
     * @return tubepress_api_service_options_OptionDescriptorReference The option descriptor reference.
     */
    public static function getOptionDescriptorReference()
    {
        return self::_lazyGet('_optionDescriptorReference', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_DESCRIPTOR_REFERENCE);
    }

    /**
     * @return tubepress_spi_options_StorageManager The option storage manager.
     */
    public static function getOptionStorageManager()
    {
        /**
         * This service is not set by the core IOC container.
         */
        return self::$_optionStorageManager;
    }

    /**
     * @return tubepress_spi_options_OptionValidator The option validator.
     */
    public static function getOptionValidator()
    {
        return self::_lazyGet('_optionValidator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_VALIDATOR);
    }

    /**
     * @return tubepress_spi_player_PlayerHtmlGenerator The player HTML generator.
     */
    public static function getPlayerHtmlGenerator()
    {
        return self::_lazyGet('_playerHtmlGenerator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLAYER_HTML_GENERATOR);
    }

    /**
     * @return tubepress_spi_plugin_PluginDiscoverer The plugin discoverer.
     */
    public static function getPluginDiscoverer()
    {
        return self::_lazyGet('_pluginDiscoverer', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLUGIN_DISCOVER);
    }

    /**
     * @return tubepress_spi_plugin_PluginRegistry The plugin registry.
     */
    public static function getPluginRegistry()
    {
        return self::_lazyGet('_pluginRegistry', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLUGIN_REGISTRY);
    }

    /**
     * @return tubepress_spi_querystring_QueryStringService The query string service.
     */
    public static function getQueryStringService()
    {
        return self::_lazyGet('_queryStringService', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_QUERY_STRING_SERVICE);
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeHtmlGenerator The shortcode HTML generator.
     */
    public static function getShortcodeHtmlGenerator()
    {
        return self::_lazyGet('_shortcodeHtmlGenerator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_HTML_GENERATOR);
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeParser The shortcode parser.
     */
    public static function getShortcodeParser()
    {
        return self::_lazyGet('_shortcodeParser', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_PARSER);
    }

    /**
     * @return ehough_contemplate_api_TemplateBuilder The template builder.
     */
    public static function getTemplateBuilder()
    {
        return self::_lazyGet('_templateBuilder', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_TEMPLATE_BUILDER);
    }

    /**
     * @return tubepress_spi_theme_ThemeHandler The theme handler.
     */
    public static function getThemeHandler()
    {
        return self::_lazyGet('_themeHandler', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_THEME_HANDLER);
    }

    /**
     * @return tubepress_spi_feed_UrlBuilder The feed URL builder.
     */
    public static function getUrlBuilder()
    {
        return self::_lazyGet('_urlBuilder', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_URL_BUILDER);
    }

    /**
     * @return tubepress_spi_factory_VideoFactory The video factory.
     */
    public static function getVideoFactory()
    {
        return self::_lazyGet('_videoFactory', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_FACTORY);
    }

    /**
     * @return tubepress_spi_provider_Provider The video provider.
     */
    public static function getVideoProvider()
    {
        return self::_lazyGet('_videoProvider', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER);
    }

    /**
     * @return tubepress_spi_provider_ProviderCalculator The video provider calculator.
     */
    public static function getVideoProviderCalculator()
    {
        return self::_lazyGet('_videoProviderCalculator', tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER_CALCULATOR);
    }

    /**
     * @param tubepress_spi_http_AjaxHandler $ajaxHandler The Ajax handler.
     */
    public static function setAjaxHandler(tubepress_spi_http_AjaxHandler $ajaxHandler)
    {
        self::$_ajaxHandler = $ajaxHandler;
    }

    /**
     * @param tubepress_spi_bootstrap_Bootstrapper $bootStrapper The bootstrapper.
     */
    public static function setBootstrapper(tubepress_spi_bootstrap_Bootstrapper $bootStrapper)
    {
        self::$_bootStrapper = $bootStrapper;
    }

    /**
     * @param ehough_stash_api_Cache $cache The cache.
     */
    public static function setCacheService(ehough_stash_api_Cache $cache)
    {
        self::$_cacheService = $cache;
    }

    /**
     * @param tubepress_spi_embedded_EmbeddedHtmlGenerator $embeddedGenerator The embedded HTML generator.
     */
    public static function setEmbeddedHtmlGenerator(tubepress_spi_embedded_EmbeddedHtmlGenerator $embeddedGenerator)
    {
        self::$_embeddedHtmlGenerator = $embeddedGenerator;
    }

    /**
     * @param tubepress_spi_environment_EnvironmentDetector $environmentDetector The environment detector.
     */
    public static function setEnvironmentDetector(tubepress_spi_environment_EnvironmentDetector $environmentDetector)
    {
        self::$_environmentDetector = $environmentDetector;
    }

    /**
     * @param ehough_tickertape_api_IEventDispatcher $eventDispatcher The event dispatcher.
     */
    public static function setEventDispatcher(ehough_tickertape_api_IEventDispatcher $eventDispatcher)
    {
        self::$_eventDispatcher = $eventDispatcher;
    }

    /**
     * @param tubepress_spi_context_ExecutionContext $executionContext The execution context.
     */
    public static function setExecutionContext(tubepress_spi_context_ExecutionContext $executionContext)
    {
        self::$_executionContext = $executionContext;
    }

    /**
     * @param tubepress_spi_feed_FeedFetcher $feedFetcher The feed fetcher.
     */
    public static function setFeedFetcher(tubepress_spi_feed_FeedFetcher $feedFetcher)
    {
        self::$_feedFetcher = $feedFetcher;
    }

    /**
     * @param tubepress_spi_feed_FeedInspector $feedInspector The feed inspector.
     */
    public static function setFeedInspector(tubepress_spi_feed_FeedInspector $feedInspector)
    {
        self::$_feedInspector = $feedInspector;
    }

    /**
     * @param ehough_fimble_api_Filesystem $fileSystem The filesystem.
     */
    public static function setFileSystem(ehough_fimble_api_Filesystem $fileSystem)
    {
        self::$_fileSystem = $fileSystem;
    }

    /**
     * @param ehough_fimble_api_FinderFactory $finderFactory The finder factory.
     */
    public static function setFileSystemFinderFactory(ehough_fimble_api_FinderFactory $finderFactory)
    {
        self::$_fileSystemFinderFactory = $finderFactory;
    }

    /**
     * @param tubepress_spi_html_HeadHtmlGenerator $headHtmlGenerator The head HTML generator.
     */
    public static function setHeadHtmlGenerator(tubepress_spi_html_HeadHtmlGenerator $headHtmlGenerator)
    {
        self::$_headHtmlGenerator = $headHtmlGenerator;
    }

    /**
     * @param ehough_shortstop_api_HttpClient $client The HTTP client.
     */
    public static function setHttpClient(ehough_shortstop_api_HttpClient $client)
    {
        self::$_httpClient = $client;
    }

    /**
     * @param ehough_shortstop_api_HttpResponseHandler $handler The HTTP response handler.
     */
    public static function setHttpResponseHandler(ehough_shortstop_api_HttpResponseHandler $handler)
    {
        self::$_httpResponseHandler = $handler;
    }

    /**
     * @param tubepress_spi_http_HttpRequestParameterService $httpRequestParameterService The HTTP request parameter service.
     */
    public static function setHttpRequestParameterService(tubepress_spi_http_HttpRequestParameterService $httpRequestParameterService)
    {
        self::$_httpRequestParameterService = $httpRequestParameterService;
    }

    /**
     * @param tubepress_spi_message_MessageService $messageService The message service.
     */
    public static function setMessageService(tubepress_spi_message_MessageService $messageService)
    {
        self::$_messageService = $messageService;
    }

    /**
     * @param tubepress_spi_options_ui_FieldBuilder $fieldBuilder The options UI field builder.
     */
    public static function setOptionsUiFieldBuilder(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        self::$_optionsUiFieldBuilder = $fieldBuilder;
    }

    /**
     * @param tubepress_spi_options_ui_FormHandler $formHandler The options UI form handler.
     */
    public static function setOptionsUiFormHandler(tubepress_spi_options_ui_FormHandler $formHandler)
    {
        self::$_optionsUiFormHandler = $formHandler;
    }

    /**
     * @param tubepress_api_service_options_OptionDescriptorReference $optionDescriptorReference The option descriptor reference.
     */
    public static function setOptionDescriptorReference(tubepress_api_service_options_OptionDescriptorReference $optionDescriptorReference)
    {
        self::$_optionDescriptorReference = $optionDescriptorReference;
    }

    /**
     * @param tubepress_spi_options_StorageManager $optionStorageManager The option storage manager.
     */
    public static function setOptionStorageManager(tubepress_spi_options_StorageManager $optionStorageManager)
    {
        self::$_optionStorageManager = $optionStorageManager;
    }

    /**
     * @param tubepress_spi_options_OptionValidator $optionValidator The option validator.
     */
    public static function setOptionValidator(tubepress_spi_options_OptionValidator $optionValidator)
    {
        self::$_optionValidator = $optionValidator;
    }

    /**
     * @param tubepress_spi_player_PlayerHtmlGenerator $playerHtmlGenerator The player HTML generator.
     */
    public static function setPlayerHtmlGenerator(tubepress_spi_player_PlayerHtmlGenerator $playerHtmlGenerator)
    {
        self::$_playerHtmlGenerator = $playerHtmlGenerator;
    }

    /**
     * @param tubepress_spi_plugin_PluginDiscoverer $pluginDiscoverer Plugin discoverer.
     */
    public static function setPluginDiscoverer(tubepress_spi_plugin_PluginDiscoverer $pluginDiscoverer)
    {
        self::$_pluginDiscoverer = $pluginDiscoverer;
    }

    /**
     * @param tubepress_spi_plugin_PluginRegistry $pluginRegistry Plugin registry.
     */
    public static function setPluginRegistry(tubepress_spi_plugin_PluginRegistry $pluginRegistry)
    {
        self::$_pluginRegistry = $pluginRegistry;
    }

    /**
     * @param tubepress_spi_querystring_QueryStringService $queryStringService The query string service.
     */
    public static function setQueryStringService(tubepress_spi_querystring_QueryStringService $queryStringService)
    {
        self::$_queryStringService = $queryStringService;
    }

    /**
     * @param tubepress_spi_shortcode_ShortcodeHtmlGenerator $shortcodeHtmlGenerator The shortcode HTML generator.
     */
    public static function setShortcodeHtmlGenerator(tubepress_spi_shortcode_ShortcodeHtmlGenerator $shortcodeHtmlGenerator)
    {
        self::$_shortcodeHtmlGenerator = $shortcodeHtmlGenerator;
    }

    /**
     * @param tubepress_spi_shortcode_ShortcodeParser $shortcodeParser The shortcode parser.
     */
    public static function setShortcodeHtmlParser(tubepress_spi_shortcode_ShortcodeParser $shortcodeParser)
    {
        self::$_shortcodeParser = $shortcodeParser;
    }

    /**
     * @param ehough_contemplate_api_TemplateBuilder $templateBuilder Template builder.
     */
    public static function setTemplateBuilder(ehough_contemplate_api_TemplateBuilder $templateBuilder)
    {
        self::$_templateBuilder = $templateBuilder;
    }

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     */
    public static function setThemeHandler(tubepress_spi_theme_ThemeHandler $themeHandler)
    {
        self::$_themeHandler = $themeHandler;
    }

    /**
     * @param tubepress_spi_feed_UrlBuilder $urlBuilder The URL builder.
     */
    public static function setUrlBuilder(tubepress_spi_feed_UrlBuilder $urlBuilder)
    {
        self::$_urlBuilder = $urlBuilder;
    }

    /**
     * @param tubepress_spi_factory_VideoFactory $videoFactory The video factory.
     */
    public static function setVideoFactory(tubepress_spi_factory_VideoFactory $videoFactory)
    {
        self::$_videoFactory = $videoFactory;
    }

    /**
     * @param tubepress_spi_provider_Provider $videoProvider The video provider.
     */
    public static function setVideoProvider(tubepress_spi_provider_Provider $videoProvider)
    {
        self::$_videoProvider = $videoProvider;
    }

    /**
     * @param tubepress_spi_provider_ProviderCalculator $videoProviderCalculator The video provider calculator.
     */
    public static function setVideoProviderCalculator(tubepress_spi_provider_ProviderCalculator $videoProviderCalculator)
    {
        self::$_videoProviderCalculator = $videoProviderCalculator;
    }


    /**
     * @param ehough_iconic_api_IContainer $container The core IOC container.
     */
    public static function setCoreIocContainer(ehough_iconic_api_IContainer $container)
    {
        self::$_coreIocContainer = $container;
    }

    /**
     * Calling this function outside of testing is suicide.
     */
    public static function reset()
    {
        self::$_coreIocContainer = null;

        self::$_ajaxHandler = null;
        self::$_bootStrapper = null;
        self::$_cacheService = null;
        self::$_embeddedHtmlGenerator = null;
        self::$_environmentDetector = null;
        self::$_eventDispatcher = null;
        self::$_executionContext = null;
        self::$_feedFetcher = null;
        self::$_feedInspector = null;
        self::$_fileSystem = null;
        self::$_fileSystemFinderFactory = null;
        self::$_headHtmlGenerator = null;
        self::$_httpClient = null;
        self::$_httpResponseHandler = null;
        self::$_httpRequestParameterService = null;
        self::$_messageService = null;
        self::$_optionDescriptorReference = null;
        self::$_optionStorageManager = null;
        self::$_optionsUiFormHandler = null;
        self::$_optionValidator = null;
        self::$_optionsUiFieldBuilder = null;
        self::$_playerHtmlGenerator = null;
        self::$_pluginDiscoverer = null;
        self::$_pluginRegistry = null;
        self::$_queryStringService = null;
        self::$_shortcodeHtmlGenerator = null;
        self::$_shortcodeParser = null;
        self::$_templateBuilder = null;
        self::$_themeHandler = null;
        self::$_urlBuilder = null;
        self::$_videoFactory = null;
        self::$_videoProvider = null;
        self::$_videoProviderCalculator = null;
    }


    private static function _lazyGet($propertyName, $iocServiceKey)
    {
        if (! isset(self::${$propertyName}) && isset(self::$_coreIocContainer)) {

            self::${$propertyName} = self::$_coreIocContainer->get($iocServiceKey);
        }

        return self::${$propertyName};
    }
}
