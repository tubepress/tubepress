<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A service locator for kernel services.
 */
class tubepress_impl_patterns_sl_ServiceLocator
{
    /**
     * @var mixed This is a special member that is a reference to the core IOC service.
     *            It lets us perform lazy lookups for core services.
     */
    private static $_iocContainer;

    /**
     * @return tubepress_spi_boot_AddonBooter
     */
    public static function getBootHelperAddonBooter()
    {
        return self::getService(tubepress_spi_boot_AddonBooter::_);
    }

    /**
     * @return tubepress_spi_boot_AddonDiscoverer The add-on discoverer.
     */
    public static function getBootHelperAddonDiscoverer()
    {
        return self::getService(tubepress_spi_boot_AddonDiscoverer::_);
    }

    /**
     * @return tubepress_spi_boot_BootConfigService
     */
    public static function getBootHelperConfigService()
    {
        return self::getService(tubepress_spi_boot_BootConfigService::_);
    }

    /**
     * @return tubepress_spi_boot_ClassLoadingHelper
     */
    public static function getBootHelperClassLoadingHelper()
    {
        return self::getService(tubepress_spi_boot_ClassLoadingHelper::_);
    }

    /**
     * @return tubepress_spi_boot_IocContainerHelper
     */
    public static function getBootHelperIocContainer()
    {
        return self::getService(tubepress_spi_boot_IocContainerHelper::_);
    }

    /**
     * @return tubepress_spi_http_AjaxHandler
     */
    public static function getAjaxHandler()
    {
        return self::getService(tubepress_spi_http_AjaxHandler::_);
    }

    /**
     * @return ehough_stash_interfaces_PoolInterface The cache service.
     */
    public static function getCacheService()
    {
        return self::getService('ehough_stash_interfaces_PoolInterface');
    }

    /**
     * @return tubepress_spi_html_CssAndJsRegistryInterface The CSS registry.
     */
    public static function getCssAndJsRegistry()
    {
        return self::getService(tubepress_spi_html_CssAndJsRegistryInterface::_);
    }

    /**
     * @return tubepress_spi_embedded_EmbeddedHtmlGenerator The embedded HTML generator.
     */
    public static function getEmbeddedHtmlGenerator()
    {
        return self::getService(tubepress_spi_embedded_EmbeddedHtmlGenerator::_);
    }

    /**
     * @return tubepress_spi_environment_EnvironmentDetector The environment detector.
     */
    public static function getEnvironmentDetector()
    {
        return self::getService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    /**
     * @return tubepress_api_event_EventDispatcherInterface The event dispatcher.
     */
    public static function getEventDispatcher()
    {
        return self::getService(tubepress_api_event_EventDispatcherInterface::_);
    }

    /**
     * @return tubepress_spi_context_ExecutionContext The execution context.
     */
    public static function getExecutionContext()
    {
        return self::getService(tubepress_spi_context_ExecutionContext::_);
    }

    /**
     * @return tubepress_spi_feed_FeedFetcher The feed fetcher.
     */
    public static function getFeedFetcher()
    {
        return self::getService(tubepress_spi_feed_FeedFetcher::_);
    }

    /**
     * @return ehough_filesystem_FilesystemInterface The filesystem service.
     */
    public static function getFileSystem()
    {
        return self::getService('ehough_filesystem_FilesystemInterface');
    }

    /**
     * @return ehough_finder_FinderFactoryInterface The finder factory.
     */
    public static function getFileSystemFinderFactory()
    {
        return self::getService('ehough_finder_FinderFactoryInterface');
    }

    /**
     * @return tubepress_spi_html_CssAndJsHtmlGeneratorInterface The head HTML generator.
     */
    public static function getCssAndJsHtmlGenerator()
    {
        return self::getService(tubepress_spi_html_CssAndJsHtmlGeneratorInterface::_);
    }

    /**
     * @return ehough_shortstop_api_HttpClientInterface The HTTP client.
     */
    public static function getHttpClient()
    {
        return self::getService('ehough_shortstop_api_HttpClientInterface');
    }

    /**
     * @return tubepress_spi_http_HttpRequestParameterService The HTTP request parameter service.
     */
    public static function getHttpRequestParameterService()
    {
        return self::getService(tubepress_spi_http_HttpRequestParameterService::_);
    }

    /**
     * @return tubepress_spi_http_ResponseCodeHandler The HTTP response code handler.
     */
    public static function getHttpResponseCodeHandler()
    {
        return self::getService(tubepress_spi_http_ResponseCodeHandler::_);
    }

    /**
     * @return tubepress_spi_message_MessageService The message service.
     */
    public static function getMessageService()
    {
        return self::getService(tubepress_spi_message_MessageService::_);
    }

    /**
     * @return tubepress_spi_options_ui_OptionsPageInterface The UI form handler.
     */
    public static function getOptionsPage()
    {
        return self::getService('tubepress_spi_options_ui_OptionsPageInterface');
    }

    /**
     * @return tubepress_spi_options_OptionDescriptorReference The option descriptor reference.
     */
    public static function getOptionDescriptorReference()
    {
        return self::getService(tubepress_spi_options_OptionDescriptorReference::_);
    }

    /**
     * @return tubepress_spi_options_StorageManager The option storage manager.
     */
    public static function getOptionStorageManager()
    {
        return self::getService(tubepress_spi_options_StorageManager::_);
    }

    /**
     * @return tubepress_spi_options_OptionValidator The option validator.
     */
    public static function getOptionValidator()
    {
        return self::getService(tubepress_spi_options_OptionValidator::_);
    }

    /**
     * @return tubepress_spi_player_PlayerHtmlGenerator The player HTML generator.
     */
    public static function getPlayerHtmlGenerator()
    {
        return self::getService(tubepress_spi_player_PlayerHtmlGenerator::_);
    }

    /**
     * @return tubepress_spi_querystring_QueryStringService The query string service.
     */
    public static function getQueryStringService()
    {
        return self::getService(tubepress_spi_querystring_QueryStringService::_);
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeHtmlGenerator The shortcode HTML generator.
     */
    public static function getShortcodeHtmlGenerator()
    {
        return self::getService(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeParser The shortcode parser.
     */
    public static function getShortcodeParser()
    {
        return self::getService(tubepress_spi_shortcode_ShortcodeParser::_);
    }

    /**
     * @return ehough_contemplate_api_TemplateBuilder The template builder.
     */
    public static function getTemplateBuilder()
    {
        return self::getService('ehough_contemplate_api_TemplateBuilder');
    }

    /**
     * @return tubepress_spi_theme_ThemeHandler The theme handler.
     */
    public static function getThemeHandler()
    {
        return self::getService(tubepress_spi_theme_ThemeHandler::_);
    }

    /**
     * @return tubepress_spi_collector_VideoCollector The video collector.
     */
    public static function getVideoCollector()
    {
        return self::getService(tubepress_spi_collector_VideoCollector::_);
    }

    /**
     * @param tubepress_api_ioc_ContainerInterface $container The core IOC container.
     */
    public static function setIocContainer(tubepress_api_ioc_ContainerInterface $container)
    {
        self::$_iocContainer = $container;
    }

    /**
     * Retrieve an arbitrary service.
     *
     * @param string $serviceId The ID of the service to retrieve.
     *
     * @return object The service instance, or null if not registered.
     */
    public static function getService($serviceId)
    {
        if (! isset(self::$_iocContainer)) {

            return null;
        }

        return self::$_iocContainer->get($serviceId);
    }
}