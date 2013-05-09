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
class tubepress_addons_core_impl_ioc_IocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function prepareForLoad()
    {
        $this->_ajaxHandler();
        $this->_cacheService();
        $this->_cssAndJs();
        $this->_embeddedGenerator();
        $this->_executionContext();
        $this->_feedFetcher();
        $this->_filesystem();
        $this->_http();
        $this->_hrps();
        $this->_hrch();
        $this->_odr();
        $this->_optionValidator();
        $this->_fieldBuilder();
        $this->_registerPlayerHtml();
        $this->_qss();
        $this->_shortcode();
        $this->_shortcodeParser();
        $this->_templateBuilder();
        $this->_themeHandler();
        $this->_videoCollector();
        $this->_pluggables();
        $this->_listeners();
        $this->_bootstrap();
    }

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_ioc_IocContainerExtension();
    }

    private function _bootstrap()
    {
        $this->expectRegistration('tubepress_addons_core_impl_Bootstrap', 'tubepress_addons_core_impl_Bootstrap');
    }

    private function _listeners()
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

            $this->expectRegistration($listenerClassName, $listenerClassName);
        }
    }

    private function _pluggables()
    {
        $this->expectRegistration('tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant', 'tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant')
            ->withTag(tubepress_spi_options_ui_PluggableOptionsPageParticipant::_);

        $this->expectRegistration('tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService', 'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService')
            ->withTag(tubepress_spi_http_PluggableAjaxCommandService::_);

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

            $this->expectRegistration($playerLocationClass, $playerLocationClass)
                ->withTag(tubepress_spi_player_PluggablePlayerLocationService::_);
        }

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_)
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'));

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService')
             ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_)
             ->withArgument(new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'));

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder', 'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder')
            ->withTag(tubepress_spi_options_ui_PluggableFieldBuilder::_);
    }

    private function _videoCollector()
    {
        $def = $this->expectRegistration(tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_collector_DefaultVideoCollector', $def);
    }

    private function _themeHandler()
    {
        $def = $this->expectRegistration(tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_theme_SimpleThemeHandler', $def);
    }

    private function _templateBuilder()
    {
        $def = $this->expectRegistration('ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder')->andReturnDefinition();
        $this->expectDefinition('ehough_contemplate_impl_SimpleTemplateBuilder', $def);
    }

    private function _shortcodeParser()
    {
        $def = $this->expectRegistration(tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_shortcode_SimpleShortcodeParser', $def);
    }

    private function _shortcode()
    {
        $def = $this->expectRegistration(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator', $def);
    }

    private function _qss()
    {
        $def = $this->expectRegistration(tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_querystring_SimpleQueryStringService', $def);
    }

    private function _registerPlayerHtml()
    {
        $def = $this->expectRegistration(tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_player_DefaultPlayerHtmlGenerator', $def);
    }

    private function _fieldBuilder()
    {
        $def = $this->expectRegistration(tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_options_ui_DefaultFieldBuilder', $def);
    }

    private function _optionValidator()
    {
        $def = $this->expectRegistration(tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_options_DefaultOptionValidator', $def);
    }

    private function _odr()
    {
        $def = $this->expectRegistration(
            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        )->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_options_DefaultOptionDescriptorReference', $def);
    }

    private function _hrch()
    {
        $def = $this->expectRegistration(tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_http_DefaultResponseCodeHandler', $def);
    }

    private function _hrps()
    {
        $def = $this->expectRegistration(tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_http_DefaultHttpRequestParameterService', $def);
    }

    private function _http()
    {
        $def = $this->expectRegistration('ehough_shortstop_spi_HttpMessageParser', 'ehough_shortstop_impl_exec_DefaultHttpMessageParser')->andReturnDefinition();
        $this->expectDefinition('ehough_shortstop_impl_exec_DefaultHttpMessageParser', $def);

        $transportClasses = array(

            'ehough_shortstop_impl_exec_command_ExtCommand',
            'ehough_shortstop_impl_exec_command_CurlCommand',
            'ehough_shortstop_impl_exec_command_StreamsCommand',
            'ehough_shortstop_impl_exec_command_FsockOpenCommand',
            'ehough_shortstop_impl_exec_command_FopenCommand'
        );

        $transportReferences = array();

        foreach ($transportClasses as $transportClass) {

            $def = $this->expectRegistration($transportClass, $transportClass)
                ->withArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpMessageParser'))
                ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

            $transportReferences[] = $def;
        }

        $transportChainDefinition = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $transportReferences);
        $transportChainDefinition->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transportChainDefinition->setFactoryMethod('buildChain');

        $this->expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_transportchain', $transportChainDefinition)
            ->withFactoryClass('tubepress_impl_ioc_ChainRegistrar')
            ->withFactoryMethod('buildChain');

        $contentDecoderCommands = array(

            'ehough_shortstop_impl_decoding_content_command_NativeGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_SimulatedGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1951DecompressingCommand',
        );

        $contentDecodingRefs = array();

        foreach ($contentDecoderCommands as $command) {

            $def = $this->expectRegistration($command, $command);
            $contentDecodingRefs[] = $def;
        }

        $contentDecodingChainDef = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $contentDecodingRefs);
        $contentDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $contentDecodingChainDef->setFactoryMethod('buildChain');

        $this->expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain', $contentDecodingChainDef)
            ->withFactoryClass('tubepress_impl_ioc_ChainRegistrar')
            ->withFactoryMethod('buildChain');

        $def = $this->expectRegistration('ehough_shortstop_spi_HttpContentDecoder', 'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain')
            ->withArgument(new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain'))
            ->andReturnDefinition();

        $this->expectDefinition('ehough_shortstop_impl_decoding_content_HttpContentDecodingChain', $def);

        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderRefs = array();

        foreach ($transferDecoderCommands as $command) {

            $def = $this->expectRegistration($command, $command);
            $transferDecoderRefs[] = $def;
        }

        $transferDecodingChainDef = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $transferDecoderRefs);
        $transferDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transferDecodingChainDef->setFactoryMethod('buildChain');

        $this->expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain', $transferDecodingChainDef)
            ->withFactoryClass('tubepress_impl_ioc_ChainRegistrar')
            ->withFactoryMethod('buildChain');

        $def = $this->expectRegistration('ehough_shortstop_spi_HttpTransferDecoder', 'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain')
            ->withArgument(new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain'))
            ->andReturnDefinition();

        $this->expectDefinition('ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain', $def);

        $this->expectRegistration('ehough_shortstop_impl_listeners_request_RequestLoggingListener', 'ehough_shortstop_impl_listeners_request_RequestLoggingListener');

        $this->expectRegistration('ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener', 'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener')
            ->withArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'));

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-transfer',
                    'ehough_shortstop_impl_listeners_response_ResponseDecodingListener')
            ->withArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpTransferDecoder'))
            ->withArgument('Transfer');

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-content',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener')
            ->withArgument(new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
            ->withArgument('Content');

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseLoggingListener', 'ehough_shortstop_impl_listeners_response_ResponseLoggingListener');

        $final = $this->expectRegistration('ehough_shortstop_api_HttpClientInterface', 'ehough_shortstop_impl_DefaultHttpClient')
             ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
             ->withArgument(new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_transportchain'))
             ->andReturnDefinition();

        $this->expectDefinition('ehough_shortstop_impl_DefaultHttpClient', $final);
    }

    private function _filesystem()
    {
        $def = $this->expectRegistration('ehough_filesystem_FilesystemInterface', 'ehough_filesystem_Filesystem')->andReturnDefinition();
        $this->expectDefinition('ehough_filesystem_Filesystem', $def);
    }

    private function _feedFetcher()
    {
        $def = $this->expectRegistration(tubepress_spi_feed_FeedFetcher::_, 'tubepress_impl_feed_CacheAwareFeedFetcher')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_feed_CacheAwareFeedFetcher', $def);
    }

    private function _executionContext()
    {
        $def = $this->expectRegistration(tubepress_spi_context_ExecutionContext::_, 'tubepress_impl_context_MemoryExecutionContext')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_context_MemoryExecutionContext', $def);
    }

    private function _embeddedGenerator()
    {
        $def = $this->expectRegistration(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator', $def);
    }

    private function _cssAndJs()
    {
        $def = $this->expectRegistration(tubepress_spi_html_CssAndJsGenerator::_, 'tubepress_impl_html_DefaultCssAndJsGenerator')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_html_DefaultCssAndJsGenerator', $def);
    }

    private function _ajaxHandler()
    {
        $def = $this->expectRegistration(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler')->andReturnDefinition();
        $this->expectDefinition('tubepress_impl_http_DefaultAjaxHandler', $def);
    }

    private function _cacheService()
    {
        $this->expectRegistration('tubepress_addons_core_impl_ioc_IocContainerExtension-_registerCacheService-builderServiceId', 'tubepress_addons_core_impl_ioc_FilesystemCacheBuilder');

        $def = new tubepress_api_ioc_Definition('ehough_stash_PoolInterface');
        $this->expectDefinition('tubepress_addons_core_impl_ioc_IocContainerExtension-_registerCacheService-actualPoolServiceId', $def);

        $mockCacheDefinition = $this->expectRegistration('ehough_stash_PoolInterface', 'tubepress_impl_cache_PoolDecorator')
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_addons_core_impl_ioc_IocContainerExtension-_registerCacheService-actualPoolServiceId'))
            ->andReturnDefinition();

        $this->expectDefinition('tubepress_impl_cache_PoolDecorator', $mockCacheDefinition);
    }
}