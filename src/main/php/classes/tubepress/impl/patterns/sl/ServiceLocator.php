<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
     * @return tubepress_spi_http_AjaxHandler
     */
    public static function getAjaxHandler()
    {
        return self::getService(tubepress_spi_http_AjaxHandler::_);
    }

    /**
     * @return ehough_stash_api_Cache The cache service.
     */
    public static function getCacheService()
    {
        return self::getService('ehough_stash_api_Cache');
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
     * @return ehough_tickertape_api_IEventDispatcher The event dispatcher.
     */
    public static function getEventDispatcher()
    {
        return self::getService('ehough_tickertape_api_IEventDispatcher');
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
     * @return ehough_fimble_api_FileSystem The filesystem service.
     */
    public static function getFileSystem()
    {
        return self::getService('ehough_fimble_api_Filesystem');
    }

    /**
     * @return ehough_fimble_api_FinderFactory The finder factory.
     */
    public static function getFileSystemFinderFactory()
    {
        return self::getService('ehough_fimble_api_FinderFactory');
    }

    /**
     * @return tubepress_spi_html_HeadHtmlGenerator The head HTML generator.
     */
    public static function getHeadHtmlGenerator()
    {
        return self::getService(tubepress_spi_html_HeadHtmlGenerator::_);
    }

    /**
     * @return ehough_shortstop_api_HttpClient The HTTP client.
     */
    public static function getHttpClient()
    {
        return self::getService('ehough_shortstop_api_HttpClient');
    }

    /**
     * @return ehough_shortstop_api_HttpResponseHandler The HTTP response handler.
     */
    public static function getHttpResponseHandler()
    {
        return self::getService('ehough_shortstop_api_HttpResponseHandler');
    }

    /**
     * @return tubepress_spi_http_HttpRequestParameterService The HTTP request parameter service.
     */
    public static function getHttpRequestParameterService()
    {
        return self::getService(tubepress_spi_http_HttpRequestParameterService::_);
    }

    /**
     * @return ehough_jameson_api_IDecoder
     */
    public static function getJsonDecoder()
    {
        return self::getService('ehough_jameson_api_IDecoder');
    }

    /**
     * @return ehough_jameson_api_IEncoder
     */
    public static function getJsonEncoder()
    {
        return self::getService('ehough_jameson_api_IEncoder');
    }

    /**
     * @return tubepress_spi_message_MessageService The message service.
     */
    public static function getMessageService()
    {
        return self::getService(tubepress_spi_message_MessageService::_);
    }

    /**
     * @return tubepress_spi_options_ui_FieldBuilder The options UI field builder.
     */
    public static function getOptionsUiFieldBuilder()
    {
        return self::getService(tubepress_spi_options_ui_FieldBuilder::_);
    }

    /**
     * @return tubepress_spi_options_ui_FormHandler The UI form handler.
     */
    public static function getOptionsUiFormHandler()
    {
        return self::getService(tubepress_spi_options_ui_FormHandler::_);
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
     * @return tubepress_spi_plugin_PluginDiscoverer The plugin discoverer.
     */
    public static function getPluginDiscoverer()
    {
        return self::getService(tubepress_spi_plugin_PluginDiscoverer::_);
    }

    /**
     * @return tubepress_spi_plugin_PluginRegistry The plugin registry.
     */
    public static function getPluginRegistry()
    {
        return self::getService(tubepress_spi_plugin_PluginRegistry::_);
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
     * @return array An array of tubepress_spi_provider_PluggableVideoProviderService
     */
    public static function getVideoProviders()
    {
        return self::getServicesWithTag(tubepress_spi_provider_PluggableVideoProviderService::_);
    }

    /**
     * @return array An array of tubepress_spi_embedded_PluggableEmbeddedPlayerService
     */
    public static function getEmbeddedPlayers()
    {
        return self::getServicesWithTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
    }

    /**
     * @return array An array of tubepress_spi_http_PluggableAjaxCommandService
     */
    public static function getAjaxCommandHandlers()
    {
        return self::getServicesWithTag(tubepress_spi_http_PluggableAjaxCommandService::_);
    }

    /**
     * @return array An array of tubepress_spi_options_ui_PluggableOptionsPageTab
     */
    public static function getOptionsPageTabs()
    {
        return self::getServicesWithTag(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME);
    }

    /**
     * @return array An array of tubepress_spi_options_ui_PluggableOptionsPageParticipant
     */
    public static function getOptionsPageParticipants()
    {
        return self::getServicesWithTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);
    }

    /**
     * @return array An array of tubepress_spi_player_PluggablePlayerLocationService
     */
    public static function getPlayerLocations()
    {
        return self::getServicesWithTag(tubepress_spi_player_PluggablePlayerLocationService::_);
    }

    /**
     * @return array An array of tubepress_spi_shortcode_PluggableShortcodeHandlerService
     */
    public static function getShortcodeHandlers()
    {
        return self::getServicesWithTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
    }



    /**
     * @param ehough_iconic_api_IContainer $container The core IOC container.
     */
    public static function setIocContainer(ehough_iconic_api_IContainer $container)
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

    /**
     * Retrieve all services that have been registered with the given tag.
     *
     * @param string $tag The tag to retrieve.
     *
     * @return array An array of service instances that have been registered with the given tag.
     *               May be empty, never null.
     */
    public static function getServicesWithTag($tag)
    {
        $toReturn = array();

        if (! isset(self::$_iocContainer)) {

            return array();
        }

        $matchingServiceIds = self::$_iocContainer->findTaggedServiceIds($tag);

        if (! $matchingServiceIds) {

            return $toReturn;
        }

        $matchingServiceIds = array_keys($matchingServiceIds);

        foreach ($matchingServiceIds as $matchingServiceId) {

            array_push($toReturn, self::getService($matchingServiceId));
        }

        return $toReturn;
    }
}