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
 * Adds shortcode handlers to TubePress.
 */
class tubepress_addons_core_impl_patterns_ioc_IocContainerExtension implements ehough_iconic_extension_ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ehough_iconic_ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws ehough_iconic_exception_InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public final function load(array $config, ehough_iconic_ContainerBuilder $container)
    {
        /**
         * Singleton services.
         */
        $this->_registerAjaxHandler($container);
        $this->_registerCacheService($container);
        $this->_registerEmbeddedHtmlGenerator($container);
        $this->_registerExecutionContext($container);
        $this->_registerFeedFetcher($container);
        $this->_registerFilesystem($container);
        $this->_registerCssAndJsGenerator($container);
        $this->_registerHttpClient($container);
        $this->_registerHttpRequestParameterService($container);
        $this->_registerHttpResponseCodeHandler($container);
        $this->_registerOptionDescriptorReference($container);
        $this->_registerOptionValidator($container);
        $this->_registerOptionsUiFieldBuilder($container);
        $this->_registerPlayerHtmlGenerator($container);
        $this->_registerQueryStringService($container);
        $this->_registerShortcodeHtmlGenerator($container);
        $this->_registerShortcodeParser($container);
        $this->_registerTemplateBuilder($container);
        $this->_registerThemeHandler($container);
        $this->_registerVideoCollector($container);

        /**
         * Pluggable services.
         */
        $this->_registerPluggableServices($container);

        /**
         * Listeners
         */
        $this->_registerListeners($container);
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public final function getAlias()
    {
        return 'core';
    }

    private function _registerAjaxHandler(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_http_AjaxHandler::_,
            'tubepress_impl_http_DefaultAjaxHandler'
        );
    }

    private function _registerCacheService(ehough_iconic_ContainerBuilder $container)
    {
        /**
         * Long, guaranteed unique service IDs (since they're anonymous)
         */
        $actualPoolServiceId = 'tubepress_addons_core_impl_patterns_ioc_IocContainerExtension-_registerCacheService-actualPoolServiceId';
        $builderServiceId    = 'tubepress_addons_core_impl_patterns_ioc_IocContainerExtension-_registerCacheService-builderServiceId';

        /**
         * First register the default cache builder.
         */
        $container->register(

            $builderServiceId,
            'tubepress_addons_core_impl_patterns_ioc_FilesystemCacheBuilder'
        );

        $actualPoolDefinition = new ehough_iconic_Definition('ehough_stash_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $container->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $container->register(

            'ehough_stash_PoolInterface',
            'tubepress_impl_cache_PoolDecorator'

        )->addArgument(new ehough_iconic_Reference($actualPoolServiceId));
    }

    private function _registerCssAndJsGenerator(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_html_CssAndJsGenerator::_,
            'tubepress_impl_html_DefaultCssAndJsGenerator'
        );
    }

    private function _registerEmbeddedHtmlGenerator(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        );
    }

    private function _registerExecutionContext(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_context_ExecutionContext::_,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFeedFetcher(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_feed_FeedFetcher::_,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerFilesystem(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );
    }

    private function _registerHttpClient(ehough_iconic_ContainerBuilder $container)
    {
        $this->_registerHttpMessageParser($container);
        $this->_registerHttpTransportChain($container);
        $this->_registerHttpContentDecoder($container);
        $this->_registerHttpTransferDecoder($container);
        $this->_registerHttpRequestDefaultHeadersListener($container);
        $this->_registerHttpRequestLoggingListener($container);
        $this->_registerHttpTransferDecodingListener($container);
        $this->_registerHttpContentDecodingListener($container);
        $this->_registerHttpResponseLoggingListener($container);

        $container->register(

            'ehough_shortstop_api_HttpClientInterface',
            'ehough_shortstop_impl_DefaultHttpClient'

        )->addArgument(new ehough_iconic_Reference('ehough_tickertape_EventDispatcherInterface'))
         ->addArgument(new ehough_iconic_Reference('_ehough_shortstop_impl_DefaultHttpClient_transportchain'));
    }

    private function _registerHttpRequestParameterService(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerHttpResponseCodeHandler(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler'
        );
    }

    private function _registerOptionDescriptorReference(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _registerOptionValidator(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator'
        );
    }

    private function _registerOptionsUiFieldBuilder(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );
    }

    private function _registerPlayerHtmlGenerator(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );
    }

    private function _registerQueryStringService(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerShortcodeHtmlGenerator(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'
        );
    }

    private function _registerShortcodeParser(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler'
        );
    }

    private function _registerVideoCollector(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        );
    }

    private function _registerPluggableServices(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant',
            'tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $container->register(

            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService',
            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService'

        )->addTag(tubepress_spi_http_PluggableAjaxCommandService::_);

        $playerLocationClasses = array(

            'tubepress_addons_core_impl_player_JqModalPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_NormalPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_PopupPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_StaticPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_VimeoPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_SoloPluggablePlayerLocationService',
            'tubepress_addons_core_impl_player_YouTubePluggablePlayerLocationService',
        );

        foreach ($playerLocationClasses as $playerLocationClass) {

            $container->register(

                $playerLocationClass, $playerLocationClass

            )->addTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }

        $container->register(

            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService'

        )->addArgument(new ehough_iconic_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'

        )->addArgument(new ehough_iconic_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'
        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder',
            'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder'
        )->addTag(tubepress_spi_options_ui_PluggableFieldBuilder::_);
    }

    private function _registerListeners(ehough_iconic_ContainerBuilder $container)
    {
        $listenerClassNames = array(

            'tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar',
            'tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener',
            'tubepress_addons_core_impl_listeners_embeddedhtml_PlayerJavaScriptApi',
            'tubepress_addons_core_impl_listeners_embeddedtemplate_CoreVariables',
            'tubepress_addons_core_impl_listeners_galleryhtml_GalleryJs',
            'tubepress_addons_core_impl_listeners_galleryinitjs_GalleryInitJsBaseParams',
            'tubepress_addons_core_impl_listeners_gallerytemplate_CoreVariables',
            'tubepress_addons_core_impl_listeners_gallerytemplate_EmbeddedPlayerName',
            'tubepress_addons_core_impl_listeners_gallerytemplate_Pagination',
            'tubepress_addons_core_impl_listeners_gallerytemplate_Player',
            'tubepress_addons_core_impl_listeners_gallerytemplate_VideoMeta',
            'tubepress_addons_core_impl_listeners_playertemplate_CoreVariables',
            'tubepress_addons_core_impl_listeners_prevalidationoptionset_StringMagic',
            'tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemover',
            'tubepress_addons_core_impl_listeners_searchinputtemplate_CoreVariables',
            'tubepress_addons_core_impl_listeners_singlevideotemplate_CoreVariables',
            'tubepress_addons_core_impl_listeners_singlevideotemplate_VideoMeta',
            'tubepress_addons_core_impl_listeners_variablereadfromexternalinput_StringMagic',
            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender',
        );

        foreach ($listenerClassNames as $listenerClassName) {

            $container->register($listenerClassName, $listenerClassName);
        }
    }

    private function _registerHttpTransportChain(ehough_iconic_ContainerBuilder $container)
    {
        /**
         * Register the transport commands and chain.
         */
        $transportClasses = array(

            'ehough_shortstop_impl_exec_command_ExtCommand',
            'ehough_shortstop_impl_exec_command_CurlCommand',
            'ehough_shortstop_impl_exec_command_StreamsCommand',
            'ehough_shortstop_impl_exec_command_FsockOpenCommand',
            'ehough_shortstop_impl_exec_command_FopenCommand'
        );

        $transportReferences = array();

        foreach ($transportClasses as $transportClass) {

            $container->register($transportClass, $transportClass)
                ->addArgument(new ehough_iconic_Reference('ehough_shortstop_spi_HttpMessageParser'))
                ->addArgument(new ehough_iconic_Reference('ehough_tickertape_EventDispatcherInterface'));

            array_push($transportReferences, new ehough_iconic_Reference($transportClass));
        }

        $transportChainId = '_ehough_shortstop_impl_DefaultHttpClient_transportchain';

        tubepress_impl_patterns_ioc_ChainRegistrar::registerChainDefinitionByReferences($container, $transportChainId, $transportReferences);
    }

    private function _registerHttpMessageParser(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_spi_HttpMessageParser',
            'ehough_shortstop_impl_exec_DefaultHttpMessageParser'
        );
    }

    private function _registerHttpResponseLoggingListener(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener',
            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener'
        );
    }

    private function _registerHttpContentDecodingListener(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener-content',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new ehough_iconic_Reference('ehough_shortstop_spi_HttpContentDecoder'))
         ->addArgument('Content');
    }

    private function _registerHttpTransferDecodingListener(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener-transfer',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new ehough_iconic_Reference('ehough_shortstop_spi_HttpTransferDecoder'))
         ->addArgument('Transfer');
    }

    private function _registerHttpRequestLoggingListener(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_request_RequestLoggingListener',
            'ehough_shortstop_impl_listeners_request_RequestLoggingListener'
        );
    }

    private function _registerHttpRequestDefaultHeadersListener(ehough_iconic_ContainerBuilder $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener',
            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener'
        )->addArgument(new ehough_iconic_Reference('ehough_shortstop_spi_HttpContentDecoder'));
    }

    private function _registerHttpContentDecoder(ehough_iconic_ContainerBuilder $container)
    {
        /**
         * Register the content decoding commands and chain.
         */
        $contentDecoderCommands = array(

            'ehough_shortstop_impl_decoding_content_command_NativeGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_SimulatedGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1951DecompressingCommand',
        );

        $contentDecoderChainId = '_ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain';

        tubepress_impl_patterns_ioc_ChainRegistrar::registerChainDefinitionByClassNames($container, $contentDecoderChainId, $contentDecoderCommands);

        /**
         * Register the content decoder.
         */
        $container->register(

            'ehough_shortstop_spi_HttpContentDecoder',
            'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain'

        )->addArgument(new ehough_iconic_Reference($contentDecoderChainId));
    }

    private function _registerHttpTransferDecoder(ehough_iconic_ContainerBuilder $container)
    {
        /**
         * Register the transfer decoding command and chain.
         */
        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderChainId = '_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain';

        tubepress_impl_patterns_ioc_ChainRegistrar::registerChainDefinitionByClassNames($container, $transferDecoderChainId, $transferDecoderCommands);

        /**
         * Register the transfer decoder.
         */
        $container->register(

            'ehough_shortstop_spi_HttpTransferDecoder',
            'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain'

        )->addArgument(new ehough_iconic_Reference($transferDecoderChainId));
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     *
     * @api
     */
    public function getNamespace()
    {
        return null;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     *
     * @api
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }
}