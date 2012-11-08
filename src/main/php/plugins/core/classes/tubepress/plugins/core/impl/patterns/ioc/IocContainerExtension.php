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
        $this->_registerHttpResponseHandler($container);
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
    }

    private function _registerCacheService(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            'ehough_stash_api_Cache',
            'ehough_stash_impl_PearCacheLiteCache'

        )->addArgument(new ehough_iconic_impl_Reference('ehough_fimble_api_Filesystem'));
    }

    private function _registerEmbeddedHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'
        );
    }

    private function _registerEventDispatcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_tickertape_api_IEventDispatcher',
            'ehough_tickertape_impl_StandardEventDispatcher'
        );
    }

    private function _registerExecutionContext(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_context_ExecutionContext::_,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFilesystem(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_fimble_api_Filesystem',
            'ehough_fimble_impl_StandardFilesystem'
        );
    }

    private function _registerFeedFetcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_feed_FeedFetcher::_,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerHeadHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_html_HeadHtmlGenerator::_,
            'tubepress_impl_html_DefaultHeadHtmlGenerator'
        );
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
    }

    private function _registerHttpResponseHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $chainId = 'ehough_shortstop_impl_HttpResponseHandlerChain_chain';

        $commands = array('tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler');

        $this->_registerChainDefinitionByClassNames($container, $chainId, $commands);

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            'ehough_shortstop_api_HttpResponseHandler',
            'ehough_shortstop_impl_HttpResponseHandlerChain'

        )->addArgument(new ehough_iconic_impl_Reference($chainId));
    }

    private function _registerJsonEncoderAndDecoder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_jameson_api_IDecoder',
            'ehough_jameson_impl_FastDecoder'
        );

        $container->register(

            'ehough_jameson_api_IEncoder',
            'ehough_jameson_impl_FastEncoder'
        );
    }

    private function _registerHttpRequestParameterService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerOptionDescriptorReference(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _registerOptionValidator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator'
        );
    }

    private function _registerOptionsUiFieldBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );
    }

    private function _registerPlayerHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );
    }

    private function _registerQueryStringService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerShortcodeHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'

        );
    }

    private function _registerShortcodeParser(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            'ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler'
        );
    }

    private function _registerVideoCollector(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector'
        );
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

        )->setFactoryClass('_tubepress_impl_patterns_ioc_CoreIocContainer_ChainBuilder')
            ->setFactoryMethod('buildChain');
    }
}
class _tubepress_impl_patterns_ioc_CoreIocContainer_ChainBuilder
{
    public static function buildChain()
    {
        $chain    = new ehough_chaingang_impl_StandardChain();
        $commands = func_get_args();

        foreach ($commands as $command) {

            $chain->addCommand($command);
        }

        return $chain;
    }
}