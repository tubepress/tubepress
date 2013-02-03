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
class tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension implements ehough_iconic_api_extension_IExtension
{

    /**
     * Loads a specific configuration.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container A ContainerBuilder instance
     *
     * @return void
     */
    public final function load(ehough_iconic_impl_ContainerBuilder $container)
    {
        /**
         * Singleton services.
         */
        $this->_registerAjaxHandler($container);
        $this->_registerCacheService($container);
        $this->_registerEmbeddedHtmlGenerator($container);
        $this->_registerEventDispatcher($container);
        $this->_registerExecutionContext($container);
        $this->_registerFeedFetcher($container);
        $this->_registerFilesystem($container);
        $this->_registerHeadHtmlGenerator($container);
        $this->_registerHttpClient($container);
        $this->_registerHttpRequestParameterService($container);
        $this->_registerJsonEncoderAndDecoder($container);
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
         * Filters
         */
        $this->_registerFilters($container);
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

    private function _registerAjaxHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_http_AjaxHandler::_,
            'tubepress_impl_http_DefaultAjaxHandler'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_http_DefaultAjaxHandler', tubepress_spi_http_AjaxHandler::_);
    }

    private function _registerCacheService(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            'ehough_stash_api_Cache',
            'ehough_stash_impl_PearCacheLiteCache'

        )->addArgument(new ehough_iconic_impl_Reference('ehough_fimble_api_Filesystem'));

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_stash_impl_PearCacheLiteCache', 'ehough_stash_api_Cache');
    }

    private function _registerEmbeddedHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator', tubepress_spi_embedded_EmbeddedHtmlGenerator::_);
    }

    private function _registerEventDispatcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_tickertape_api_IEventDispatcher',
            'ehough_tickertape_impl_StandardEventDispatcher'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_tickertape_impl_StandardEventDispatcher', 'ehough_tickertape_api_IEventDispatcher');
    }

    private function _registerExecutionContext(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_context_ExecutionContext::_,
            'tubepress_impl_context_MemoryExecutionContext'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_context_MemoryExecutionContext', tubepress_spi_context_ExecutionContext::_);
    }

    private function _registerFilesystem(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_fimble_api_Filesystem',
            'ehough_fimble_impl_StandardFilesystem'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_fimble_impl_StandardFilesystem', 'ehough_fimble_api_Filesystem');
    }

    private function _registerFeedFetcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_feed_FeedFetcher::_,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_feed_CacheAwareFeedFetcher', tubepress_spi_feed_FeedFetcher::_);
    }

    private function _registerHeadHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_html_HeadHtmlGenerator::_,
            'tubepress_impl_html_DefaultHeadHtmlGenerator'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_html_DefaultHeadHtmlGenerator', tubepress_spi_html_HeadHtmlGenerator::_);
    }

    private function _registerHttpClient(ehough_iconic_impl_ContainerBuilder $container)
    {
        /**
         * This is a complicated function. Take your time!
         */

        /**
         * Register the message parser.
         */
        $messageParserId = 'ehough_shortstop_impl_DefaultHttpMessageParser';
        $container->register($messageParserId, $messageParserId);


        /**
         * Register the transport commands and chain.
         */
        $transportClasses = array(

            'ehough_shortstop_impl_transports_ExtHttpTransport',
            'ehough_shortstop_impl_transports_CurlTransport',
            'ehough_shortstop_impl_transports_StreamsTransport',
            'ehough_shortstop_impl_transports_FsockOpenTransport',
            'ehough_shortstop_impl_transports_FopenTransport'
        );

        $transportReferences = array();

        foreach ($transportClasses as $transportClass) {

            /** @noinspection PhpUndefinedMethodInspection */
            $container->register($transportClass, $transportClass)
                ->addArgument(new ehough_iconic_impl_Reference($messageParserId));

            array_push($transportReferences, new ehough_iconic_impl_Reference($transportClass));
        }

        $transportChainId = '_ehough_shortstop_impl_HttpClientChain_transportchain';

        $this->_registerChainDefinitionByReferences($container, $transportChainId, $transportReferences);

        /**
         * Register the content decoding commands and chain.
         */
        $contentDecoderCommands = array(

            'ehough_shortstop_impl_contentencoding_NativeGzipDecompressor',
            'ehough_shortstop_impl_contentencoding_SimulatedGzipDecompressor',
            'ehough_shortstop_impl_contentencoding_NativeDeflateRfc1950Decompressor',
            'ehough_shortstop_impl_contentencoding_NativeDeflateRfc1951Decompressor',
        );

        $contentDecoderChainId = '_ehough_shortstop_impl_HttpClientChain_contentdecoderchain';

        $this->_registerChainDefinitionByClassNames($container, $contentDecoderChainId, $contentDecoderCommands);


        /**
         * Register the transfer decoding command and chain.
         */
        $transferDecoderCommands = array(

            'ehough_shortstop_impl_transferencoding_ChunkedTransferDecoder'
        );

        $transferDecoderChainId = '_ehough_shortstop_impl_HttpClientChain_transferdecoderchain';

        $this->_registerChainDefinitionByClassNames($container, $transferDecoderChainId, $transferDecoderCommands);


        /**
         * Register the transfer decoder.
         */
        $transferDecoderId = '_ehough_shortstop_impl_HttpClientChain_transferdecoder';

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            $transferDecoderId, 'ehough_shortstop_impl_HttpTransferDecoderChain'

        )->addArgument(new ehough_iconic_impl_Reference($transferDecoderChainId));


        /**
         * Register the content decoder.
         */
        $contentDecoderId = '_ehough_shortstop_impl_HttpClientChain_contentdecoder';

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            $contentDecoderId, 'ehough_shortstop_impl_HttpContentDecoderChain'

        )->addArgument(new ehough_iconic_impl_Reference($contentDecoderChainId));


        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            'ehough_shortstop_api_HttpClient',
            'ehough_shortstop_impl_HttpClientChain'

        )->addArgument(new ehough_iconic_impl_Reference($transportChainId))
            ->addArgument(new ehough_iconic_impl_Reference($contentDecoderId))
            ->addArgument(new ehough_iconic_impl_Reference($transferDecoderId));

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_shortstop_impl_HttpClientChain', 'ehough_shortstop_api_HttpClient');
    }

    private function _registerJsonEncoderAndDecoder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_jameson_api_IDecoder',
            'ehough_jameson_impl_FastDecoder'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_jameson_impl_FastDecoder', 'ehough_jameson_api_IDecoder');

        $container->register(

            'ehough_jameson_api_IEncoder',
            'ehough_jameson_impl_FastEncoder'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_jameson_impl_FastEncoder', 'ehough_jameson_api_IEncoder');
    }

    private function _registerHttpRequestParameterService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_http_DefaultHttpRequestParameterService', tubepress_spi_http_HttpRequestParameterService::_);
    }

    private function _registerOptionDescriptorReference(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_options_DefaultOptionDescriptorReference', tubepress_spi_options_OptionDescriptorReference::_);
    }

    private function _registerOptionValidator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_options_DefaultOptionValidator', tubepress_spi_options_OptionValidator::_);
    }

    private function _registerOptionsUiFieldBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_options_ui_DefaultFieldBuilder', tubepress_spi_options_ui_FieldBuilder::_);
    }

    private function _registerPlayerHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_player_DefaultPlayerHtmlGenerator', tubepress_spi_player_PlayerHtmlGenerator::_);
    }

    private function _registerQueryStringService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_querystring_SimpleQueryStringService', tubepress_spi_querystring_QueryStringService::_);
    }

    private function _registerShortcodeHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator', tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
    }

    private function _registerShortcodeParser(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_shortcode_SimpleShortcodeParser', tubepress_spi_shortcode_ShortcodeParser::_);
    }

    private function _registerTemplateBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('ehough_contemplate_impl_SimpleTemplateBuilder', 'ehough_contemplate_api_TemplateBuilder');
    }

    private function _registerThemeHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_theme_SimpleThemeHandler', tubepress_spi_theme_ThemeHandler::_);
    }

    private function _registerVideoCollector(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        );

        /* Allows for convenient access to this definition by IOC extensions. */
        $container->setAlias('tubepress_impl_collector_DefaultVideoCollector', tubepress_spi_collector_VideoCollector::_);
    }

    private function _registerPluggableServices(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'tubepress_plugins_core_impl_options_ui_CoreOptionsPageParticipant',
            'tubepress_plugins_core_impl_options_ui_CoreOptionsPageParticipant'

        )->addTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $container->register(

            'tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService',
            'tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService'

        )->addTag(tubepress_spi_http_PluggableAjaxCommandService::_);

        $playerLocationClasses = array(

            'tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_VimeoPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_SoloPluggablePlayerLocationService',
            'tubepress_plugins_core_impl_player_YouTubePluggablePlayerLocationService',
        );

        foreach ($playerLocationClasses as $playerLocationClass) {

            $container->register(

                $playerLocationClass, $playerLocationClass

            )->addTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }

        $container->register(

            'tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService',
            'tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService',
            'tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService'

        )->addArgument(new ehough_iconic_impl_Reference('tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService',
            'tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'

        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService',
            'tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'

        )->addArgument(new ehough_iconic_impl_Reference('tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'))
         ->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $container->register(

            'tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService',
            'tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'
        )->addTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
    }

    private function _registerFilters(ehough_iconic_impl_ContainerBuilder $container)
    {
        $filterClasses = array(

            'tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi',
            'tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables',
            'tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs',
            'tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams',
            'tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables',
            'tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName',
            'tubepress_plugins_core_impl_filters_gallerytemplate_Pagination',
            'tubepress_plugins_core_impl_filters_gallerytemplate_Player',
            'tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta',
            'tubepress_plugins_core_impl_filters_playertemplate_CoreVariables',
            'tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic',
            'tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover',
            'tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables',
            'tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables',
            'tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta',
            'tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic',
            'tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter',
            'tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper',
            'tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist',
            'tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender',
        );

        foreach ($filterClasses as $filterClass) {

            $container->register($filterClass, $filterClass);
        }
    }

    private function _registerChainDefinitionByClassNames(ehough_iconic_impl_ContainerBuilder $container, $chainName, array $classNames)
    {
        $references = array();

        foreach ($classNames as $className) {

            $container->register($className, $className);

            array_push($references, new ehough_iconic_impl_Reference($className));
        }

        $this->_registerChainDefinitionByReferences($container, $chainName, $references);
    }

    private function _registerChainDefinitionByReferences(ehough_iconic_impl_ContainerBuilder $container, $chainName, array $references)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->setDefinition(

            $chainName,
            new ehough_iconic_impl_Definition(

                'ehough_chaingang_api_Chain',
                $references
            )

        )->setFactoryClass('tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension')
         ->setFactoryMethod('_buildChain');
    }

    public static function _buildChain()
    {
        $chain    = new ehough_chaingang_impl_StandardChain();
        $commands = func_get_args();

        foreach ($commands as $command) {

            $chain->addCommand($command);
        }

        return $chain;
    }
}