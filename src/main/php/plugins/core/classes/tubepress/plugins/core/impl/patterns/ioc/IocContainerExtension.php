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
        $this->_registerOptionDescriptorReference($container);
        $this->_registerOptionValidator($container);
        $this->_registerOptionsUiFieldBuilder($container);
        $this->_registerPlayerHtmlGenerator($container);
        $this->_registerQueryStringService($container);
        $this->_registerServiceCollectionsRegistry($container);
        $this->_registerShortcodeHtmlGenerator($container);
        $this->_registerShortcodeParser($container);
        $this->_registerTemplateBuilder($container);
        $this->_registerThemeHandler($container);
        $this->_registerVideoCollector($container);
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

            tubepress_spi_const_patterns_ioc_ServiceIds::AJAX_HANDLER,
            'tubepress_impl_http_DefaultAjaxHandler'

        );
    }

    private function _registerCacheService(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::CACHE,
            'ehough_stash_impl_PearCacheLiteCache'

        )->addArgument(new ehough_iconic_impl_Reference(tubepress_spi_const_patterns_ioc_ServiceIds::FILESYSTEM));
    }

    private function _registerEmbeddedHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::EMBEDDED_HTML_GENERATOR,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'

        );
    }

    private function _registerEventDispatcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::EVENT_DISPATCHER,
            'ehough_tickertape_impl_StandardEventDispatcher'
        );
    }

    private function _registerExecutionContext(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::EXECUTION_CONTEXT,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFilesystem(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::FILESYSTEM,
            'ehough_fimble_impl_StandardFilesystem'
        );
    }



    private function _registerFeedFetcher(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::FEED_FETCHER,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerHeadHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::HEAD_HTML_GENERATOR,
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

            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_CLIENT,
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

            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_RESPONSE_HANDLER,
            'ehough_shortstop_impl_HttpResponseHandlerChain'

        )->addArgument(new ehough_iconic_impl_Reference($chainId));
    }

    private function _registerHttpRequestParameterService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_REQUEST_PARAMS,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerOptionDescriptorReference(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::OPTION_DESCRIPTOR_REFERENCE,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _registerOptionValidator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::OPTION_VALIDATOR,
            'tubepress_impl_options_DefaultOptionValidator'
        );
    }

    private function _registerOptionsUiFieldBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::OPTIONS_UI_FIELDBUILDER,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );
    }

    private function _registerPlayerHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::PLAYER_HTML_GENERATOR,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );
    }

    private function _registerQueryStringService(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::QUERY_STRING_SERVICE,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerServiceCollectionsRegistry(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::SERVICE_COLLECTIONS_REGISTRY,
            'tubepress_impl_patterns_sl_DefaultServiceCollectionsRegistry'
        );
    }

    private function _registerShortcodeHtmlGenerator(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::SHORTCODE_HTML_GENERATOR,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'

        );
    }

    private function _registerShortcodeParser(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::SHORTCODE_PARSER,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::TEMPLATE_BUILDER,
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::THEME_HANDLER,
            'tubepress_impl_theme_SimpleThemeHandler'
        );
    }

    private function _registerVideoCollector(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::VIDEO_COLLECTOR,
            'tubepress_impl_collector_DefaultVideoCollector'
        );
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