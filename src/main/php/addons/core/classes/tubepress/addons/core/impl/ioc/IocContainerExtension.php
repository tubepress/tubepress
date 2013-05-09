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
class tubepress_addons_core_impl_ioc_IocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param tubepress_api_ioc_ContainerInterface $container A ContainerBuilder instance
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    public final function load(tubepress_api_ioc_ContainerInterface $container)
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

        /**
         * Bootstrap.
         */
        $this->_registerBootstrapper($container);
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

    private function _registerAjaxHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_http_AjaxHandler::_,
            'tubepress_impl_http_DefaultAjaxHandler'
        );
    }

    private function _registerCacheService(tubepress_api_ioc_ContainerInterface $container)
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

        $actualPoolDefinition = new tubepress_api_ioc_Definition('ehough_stash_PoolInterface');
        $actualPoolDefinition->setFactoryService($builderServiceId);
        $actualPoolDefinition->setFactoryMethod('buildCache');
        $container->setDefinition($actualPoolServiceId, $actualPoolDefinition);

        $definition = $container->register(

            'ehough_stash_PoolInterface',
            'tubepress_impl_cache_PoolDecorator'

        )->addArgument(new tubepress_api_ioc_Reference($actualPoolServiceId));

        $container->setDefinition('tubepress_impl_cache_PoolDecorator', $definition);
    }

    private function _registerCssAndJsGenerator(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_html_CssAndJsGenerator::_,
            'tubepress_impl_html_DefaultCssAndJsGenerator'
        );
    }

    private function _registerEmbeddedHtmlGenerator(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        );
    }

    private function _registerExecutionContext(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_context_ExecutionContext::_,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFeedFetcher(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_feed_FeedFetcher::_,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerFilesystem(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );
    }

    private function _registerHttpClient(tubepress_api_ioc_ContainerInterface $container)
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

        $definition = $container->register(

            'ehough_shortstop_api_HttpClientInterface',
            'ehough_shortstop_impl_DefaultHttpClient'

        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_transportchain'));

        $container->setDefinition('ehough_shortstop_impl_DefaultHttpClient', $definition);
    }

    private function _registerHttpRequestParameterService(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerHttpResponseCodeHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler'
        );
    }

    private function _registerOptionDescriptorReference(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _registerOptionValidator(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator'
        );
    }

    private function _registerOptionsUiFieldBuilder(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );
    }

    private function _registerPlayerHtmlGenerator(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );
    }

    private function _registerQueryStringService(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerShortcodeHtmlGenerator(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'
        );
    }

    private function _registerShortcodeParser(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler'
        );
    }

    private function _registerVideoCollector(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        );
    }

    private function _registerPluggableServices(tubepress_api_ioc_ContainerInterface $container)
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

        )->addArgument(new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'

        )->addArgument(new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'))
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

    private function _registerListeners(tubepress_api_ioc_ContainerInterface $container)
    {
        $listenerClassNames = array(

            'tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar',
            'tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener',
            'tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJs',
            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables',
            'tubepress_addons_core_impl_listeners_html_JsConfig',
            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs',
            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation',
            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta',
            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables',
            'tubepress_addons_core_impl_listeners_options_PreValidationOptionSetStringMagic',
            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover',
            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables',
            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta',
            'tubepress_addons_core_impl_listeners_options_ExternalInputStringMagic',
            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter',
            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender',
        );

        foreach ($listenerClassNames as $listenerClassName) {

            $container->register($listenerClassName, $listenerClassName);
        }
    }

    private function _registerHttpTransportChain(tubepress_api_ioc_ContainerInterface $container)
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
                ->addArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpMessageParser'))
                ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

            array_push($transportReferences, new tubepress_api_ioc_Reference($transportClass));
        }

        $transportChainId = '_ehough_shortstop_impl_DefaultHttpClient_transportchain';

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByReferences($container, $transportChainId, $transportReferences);
    }

    private function _registerHttpMessageParser(tubepress_api_ioc_ContainerInterface $container)
    {
        $this->_registerSimpleService(

            $container,
            'ehough_shortstop_spi_HttpMessageParser',
            'ehough_shortstop_impl_exec_DefaultHttpMessageParser'
        );
    }

    private function _registerHttpResponseLoggingListener(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener',
            'ehough_shortstop_impl_listeners_response_ResponseLoggingListener'
        );
    }

    private function _registerHttpContentDecodingListener(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener-content',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
         ->addArgument('Content');
    }

    private function _registerHttpTransferDecodingListener(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener-transfer',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'
        )->addArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpTransferDecoder'))
         ->addArgument('Transfer');
    }

    private function _registerHttpRequestLoggingListener(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_request_RequestLoggingListener',
            'ehough_shortstop_impl_listeners_request_RequestLoggingListener'
        );
    }

    private function _registerHttpRequestDefaultHeadersListener(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener',
            'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener'
        )->addArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'));
    }

    private function _registerHttpContentDecoder(tubepress_api_ioc_ContainerInterface $container)
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

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByClassNames($container, $contentDecoderChainId, $contentDecoderCommands);

        /**
         * Register the content decoder.
         */
        $definition = $container->register(

            'ehough_shortstop_spi_HttpContentDecoder',
            'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain'

        )->addArgument(new tubepress_api_ioc_Reference($contentDecoderChainId));

        $container->setDefinition('ehough_shortstop_impl_decoding_content_HttpContentDecodingChain', $definition);
    }

    private function _registerHttpTransferDecoder(tubepress_api_ioc_ContainerInterface $container)
    {
        /**
         * Register the transfer decoding command and chain.
         */
        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderChainId = '_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain';

        tubepress_impl_ioc_ChainRegistrar::registerChainDefinitionByClassNames($container, $transferDecoderChainId, $transferDecoderCommands);

        /**
         * Register the transfer decoder.
         */
        $definition = $container->register(

            'ehough_shortstop_spi_HttpTransferDecoder',
            'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain'

        )->addArgument(new tubepress_api_ioc_Reference($transferDecoderChainId));

        $container->setDefinition('ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain', $definition);
    }

    private function _registerBootstrapper(tubepress_api_ioc_ContainerInterface $container)
    {
        $container->register(

            'tubepress_addons_core_impl_Bootstrap',
            'tubepress_addons_core_impl_Bootstrap'
        );
    }

    private function _registerSimpleService(tubepress_api_ioc_ContainerInterface $container, $id, $class)
    {
        $definition = $container->register($id, $class);

        $container->setDefinition($class, $definition);
    }
}