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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
final class tubepress_impl_patterns_ioc_CoreIocContainer extends tubepress_impl_patterns_ioc_AbstractReadOnlyIocContainer
{
    const SERVICE_AJAX_HANDLER                 = 'ajaxHandler';
    const SERVICE_BOOTSTRAPPER                 = 'bootStrapper';
    const SERVICE_CACHE                        = 'cacheService';
    const SERVICE_EMBEDDED_HTML_GENERATOR      = 'embeddedHtmlGenerator';
    const SERVICE_ENVIRONMENT_DETECTOR         = 'environmentDetector';
    const SERVICE_EVENT_DISPATCHER             = 'eventDispatcher';
    const SERVICE_EXECUTION_CONTEXT            = 'executionContext';
    const SERVICE_FEED_FETCHER                 = 'feedFetcher';
    const SERVICE_FILESYSTEM                   = 'fileSystem';
    const SERVICE_FILESYSTEM_FINDER_FACTORY    = 'fileSystemFinderFactory';
    const SERVICE_HEAD_HTML_GENERATOR          = 'headHtmlGenerator';
    const SERVICE_HTTP_CLIENT                  = 'httpClient';
    const SERVICE_HTTP_RESPONSE_HANDLER        = 'httpResponseHandler';
    const SERVICE_HTTP_REQUEST_PARAMS          = 'httpRequestParameterService';
    const SERVICE_OPTION_DESCRIPTOR_REFERENCE  = 'optionDescriptorReference';
    const SERVICE_OPTION_VALIDATOR             = 'optionValidator';
    const SERVICE_OPTIONS_UI_FIELDBUILDER      = 'optionsUiFieldBuilder';
    const SERVICE_PLAYER_HTML_GENERATOR        = 'playerHtmlGenerator';
    const SERVICE_PLUGIN_DISCOVER              = 'pluginDiscoverer';
    const SERVICE_PLUGIN_REGISTRY              = 'pluginRegistry';
    const SERVICE_QUERY_STRING_SERVICE         = 'queryStringService';
    const SERVICE_SERVICE_COLLECTIONS_REGISTRY = 'serviceCollectionsRegistry';
    const SERVICE_SHORTCODE_HTML_GENERATOR     = 'shortcodeHtmlGenerator';
    const SERVICE_SHORTCODE_PARSER             = 'shortcodeParser';
    const SERVICE_TEMPLATE_BUILDER             = 'templateBuilder';
    const SERVICE_THEME_HANDLER                = 'themeHandler';
    const SERVICE_VIDEO_COLLECTOR              = 'videoCollector';

    /**
     * @var ehough_iconic_api_IContainer
     */
    private $_delegate;

    public function __construct()
    {
        $this->_delegate = new ehough_iconic_impl_ContainerBuilder();

        $this->_registerAjaxHandler();
        $this->_registerBootstrapper();
        $this->_registerCacheService();
        $this->_registerEmbeddedHtmlGenerator();
        $this->_registerEnvironmentDetector();
        $this->_registerEventDispatcher();
        $this->_registerExecutionContext();
        $this->_registerFeedFetcher();
        $this->_registerFilesystem();
        $this->_registerFilesystemFinderFactory();
        $this->_registerHeadHtmlGenerator();
        $this->_registerHttpClient();
        $this->_registerHttpResponseHandler();
        $this->_registerHttpRequestParameterService();
        $this->_registerOptionDescriptorReference();
        $this->_registerOptionValidator();
        $this->_registerOptionsUiFieldBuilder();
        $this->_registerPlayerHtmlGenerator();
        $this->_registerPluginDiscoverer();
        $this->_registerPluginRegistry();
        $this->_registerQueryStringService();
        $this->_registerServiceCollectionsRegistry();
        $this->_registerShortcodeHtmlGenerator();
        $this->_registerShortcodeParser();
        $this->_registerTemplateBuilder();
        $this->_registerThemeHandler();
        $this->_registerVideoCollector();
    }

    private function _registerAjaxHandler()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_AJAX_HANDLER,
            'tubepress_impl_http_DefaultAjaxHandler'

        );
    }

    private function _registerBootstrapper()
    {
        $this->_delegate->register(

            self::SERVICE_BOOTSTRAPPER,
            'tubepress_impl_bootstrap_TubePressBootstrapper'
        );
    }

    private function _registerCacheService()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_CACHE,
            'ehough_stash_impl_PearCacheLiteCache'

        )->addArgument(new ehough_iconic_impl_Reference(self::SERVICE_FILESYSTEM));
    }

    private function _registerEmbeddedHtmlGenerator()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_EMBEDDED_HTML_GENERATOR,
            'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'

        );
    }

    private function _registerEnvironmentDetector()
    {
        $this->_delegate->register(

            self::SERVICE_ENVIRONMENT_DETECTOR,
            'tubepress_impl_environment_SimpleEnvironmentDetector'
        );
    }

    private function _registerEventDispatcher()
    {
        $this->_delegate->register(

            self::SERVICE_EVENT_DISPATCHER,
            'ehough_tickertape_impl_StandardEventDispatcher'
        );
    }

    private function _registerExecutionContext()
    {
        $this->_delegate->register(

            self::SERVICE_EXECUTION_CONTEXT,
            'tubepress_impl_context_MemoryExecutionContext'
        );
    }

    private function _registerFeedFetcher()
    {
        $this->_delegate->register(

            self::SERVICE_FEED_FETCHER,
            'tubepress_impl_feed_CacheAwareFeedFetcher'
        );
    }

    private function _registerFilesystem()
    {
        $this->_delegate->register(

            self::SERVICE_FILESYSTEM,
            'ehough_fimble_impl_StandardFilesystem'
        );
    }

    private function _registerFilesystemFinderFactory()
    {
        $this->_delegate->register(

            self::SERVICE_FILESYSTEM_FINDER_FACTORY,
            'ehough_fimble_impl_StandardFinderFactory'
        );
    }

    private function _registerHeadHtmlGenerator()
    {
        $this->_delegate->register(

            self::SERVICE_HEAD_HTML_GENERATOR,
            'tubepress_impl_html_DefaultHeadHtmlGenerator'
        );
    }

    private function _registerHttpClient()
    {
        /**
         * This is a complicated function. Take your time!
         */

        /**
         * Register the message parser.
         */
        $messageParserId = 'ehough_shortstop_impl_DefaultHttpMessageParser';
        $this->_delegate->register($messageParserId, $messageParserId);


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
            $this->_delegate->register($transportClass, $transportClass)
                            ->addArgument(new ehough_iconic_impl_Reference($messageParserId));

            array_push($transportReferences, new ehough_iconic_impl_Reference($transportClass));
        }

        $transportChainId = '_ehough_shortstop_impl_HttpClientChain_transportchain';

        $this->_registerChainDefinitionByReferences($transportChainId, $transportReferences);


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

        $this->_registerChainDefinitionByClassNames($contentDecoderChainId, $contentDecoderCommands);


        /**
         * Register the transfer decoding command and chain.
         */
        $transferDecoderCommands = array(

            'ehough_shortstop_impl_transferencoding_ChunkedTransferDecoder'
        );

        $transferDecoderChainId = '_ehough_shortstop_impl_HttpClientChain_transferdecoderchain';

        $this->_registerChainDefinitionByClassNames($transferDecoderChainId, $transferDecoderCommands);


        /**
         * Register the transfer decoder.
         */
        $transferDecoderId = '_ehough_shortstop_impl_HttpClientChain_transferdecoder';

        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            $transferDecoderId, 'ehough_shortstop_impl_HttpTransferDecoderChain'

        )->addArgument(new ehough_iconic_impl_Reference($transferDecoderChainId));


        /**
         * Register the content decoder.
         */
        $contentDecoderId = '_ehough_shortstop_impl_HttpClientChain_contentdecoder';

        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            $contentDecoderId, 'ehough_shortstop_impl_HttpContentDecoderChain'

        )->addArgument(new ehough_iconic_impl_Reference($contentDecoderChainId));


        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_HTTP_CLIENT,
            'ehough_shortstop_impl_HttpClientChain'

        )->addArgument(new ehough_iconic_impl_Reference($transportChainId))
         ->addArgument(new ehough_iconic_impl_Reference($contentDecoderId))
         ->addArgument(new ehough_iconic_impl_Reference($transferDecoderId));
    }

    private function _registerHttpResponseHandler()
    {
        $chainId = 'ehough_shortstop_impl_HttpResponseHandlerChain_chain';

        $commands = array('tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler');

        $this->_registerChainDefinitionByClassNames($chainId, $commands);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_HTTP_RESPONSE_HANDLER,
            'ehough_shortstop_impl_HttpResponseHandlerChain'

        )->addArgument(new ehough_iconic_impl_Reference($chainId));
    }

    private function _registerHttpRequestParameterService()
    {
        $this->_delegate->register(

            self::SERVICE_HTTP_REQUEST_PARAMS,
            'tubepress_impl_http_DefaultHttpRequestParameterService'
        );
    }

    private function _registerOptionDescriptorReference()
    {
        $this->_delegate->register(

            self::SERVICE_OPTION_DESCRIPTOR_REFERENCE,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _registerOptionValidator()
    {
        $this->_delegate->register(

            self::SERVICE_OPTION_VALIDATOR,
            'tubepress_impl_options_DefaultOptionValidator'
        );
    }

    private function _registerOptionsUiFieldBuilder()
    {
        $this->_delegate->register(

            self::SERVICE_OPTIONS_UI_FIELDBUILDER,
            'tubepress_impl_options_ui_DefaultFieldBuilder'
        );
    }

    private function _registerPlayerHtmlGenerator()
    {
        $this->_delegate->register(

            self::SERVICE_PLAYER_HTML_GENERATOR,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator'
        );
    }

    private function _registerPluginDiscoverer()
    {
        $this->_delegate->register(

            self::SERVICE_PLUGIN_DISCOVER,
            'tubepress_impl_plugin_FilesystemPluginDiscoverer'
        );
    }

    private function _registerPluginRegistry()
    {
        $this->_delegate->register(

            self::SERVICE_PLUGIN_REGISTRY,
            'tubepress_impl_plugin_DefaultPluginRegistry'
        );
    }

    private function _registerQueryStringService()
    {
        $this->_delegate->register(

            self::SERVICE_QUERY_STRING_SERVICE,
            'tubepress_impl_querystring_SimpleQueryStringService'
        );
    }

    private function _registerServiceCollectionsRegistry()
    {
        $this->_delegate->register(

            self::SERVICE_SERVICE_COLLECTIONS_REGISTRY,
            'tubepress_impl_patterns_sl_DefaultServiceCollectionsRegistry'
        );
    }

    private function _registerShortcodeHtmlGenerator()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_SHORTCODE_HTML_GENERATOR,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'

        );
    }

    private function _registerShortcodeParser()
    {
        $this->_delegate->register(

            self::SERVICE_SHORTCODE_PARSER,
            'tubepress_impl_shortcode_SimpleShortcodeParser'
        );
    }

    private function _registerTemplateBuilder()
    {
        $this->_delegate->register(

            self::SERVICE_TEMPLATE_BUILDER,
            'ehough_contemplate_impl_SimpleTemplateBuilder'
        );
    }

    private function _registerThemeHandler()
    {
        $this->_delegate->register(

            self::SERVICE_THEME_HANDLER,
            'tubepress_impl_theme_SimpleThemeHandler'
        );
    }

    private function _registerVideoCollector()
    {
        $this->_delegate->register(

            self::SERVICE_VIDEO_COLLECTOR,
            'tubepress_impl_collector_DefaultVideoCollector'
        );
    }

    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException if the service is not defined
     */
    public final function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->_delegate->get($id, $invalidBehavior);
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return boolean True if the service is defined, false otherwise
     */
    public final function has($id)
    {
        return $this->_delegate->has($id);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public final function getParameter($name)
    {
        return $this->_delegate->getParameter($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return boolean The presence of parameter in container
     */
    public final function hasParameter($name)
    {
        return $this->_delegate->hasParameter($name);
    }

    private function _registerChainDefinitionByClassNames($chainName, array $classNames)
    {
        $references = array();

        foreach ($classNames as $className) {

            $this->_delegate->register($className, $className);

            array_push($references, new ehough_iconic_impl_Reference($className));
        }

        $this->_registerChainDefinitionByReferences($chainName, $references);
    }

    private function _registerChainDefinitionByReferences($chainName, array $references)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->setDefinition(

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