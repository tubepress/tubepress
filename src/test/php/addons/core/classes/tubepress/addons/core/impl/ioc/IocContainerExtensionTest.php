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
class tubepress_addons_core_impl_ioc_IocContainerExtensionTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_ioc_IocContainerExtension
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_ioc_IocContainerExtension();

        $this->_mockContainer = ehough_mockery_Mockery::mock(tubepress_api_ioc_ContainerInterface::_);
    }

    public function testGetAlias()
    {
        $this->assertEquals('core', $this->_sut->getAlias());
    }

    public function testLoad()
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

        $this->_sut->load($this->_mockContainer);

        $this->assertTrue(true);
    }

    private function _bootstrap()
    {
        $this->_expectRegistration('tubepress_addons_core_impl_Bootstrap', 'tubepress_addons_core_impl_Bootstrap');
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

            $this->_expectRegistration($listenerClassName, $listenerClassName);
        }
    }

    private function _pluggables()
    {
        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant', 'tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant'),
            tubepress_spi_options_ui_PluggableOptionsPageParticipant::_
        );

        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService', 'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService'),
            tubepress_spi_http_PluggableAjaxCommandService::_
        );

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

            $this->_expectTag(
                $this->_expectRegistration($playerLocationClass, $playerLocationClass),
                tubepress_spi_player_PluggablePlayerLocationService::_
            );
        }

        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService'),
            tubepress_spi_shortcode_PluggableShortcodeHandlerService::_
        );

        $this->_expectArgument(
            $this->_expectTag(
                $this->_expectRegistration('tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService'),
                tubepress_spi_shortcode_PluggableShortcodeHandlerService::_
            ),
            new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService')
        );

        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'),
            tubepress_spi_shortcode_PluggableShortcodeHandlerService::_
        );

        $this->_expectArgument(
            $this->_expectTag(
                $this->_expectRegistration('tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService'),
                tubepress_spi_shortcode_PluggableShortcodeHandlerService::_
            ),
            new tubepress_api_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService')
        );

        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'),
            tubepress_spi_shortcode_PluggableShortcodeHandlerService::_
        );

        $this->_expectTag(
            $this->_expectRegistration('tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder', 'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder'),
            tubepress_spi_options_ui_PluggableFieldBuilder::_
        );
    }

    private function _videoCollector()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector');
    }

    private function _themeHandler()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler');
    }

    private function _templateBuilder()
    {
        $this->_expectRegistrationAndDefinition('ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder');
    }

    private function _shortcodeParser()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser');
    }

    private function _shortcode()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator');
    }

    private function _qss()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService');
    }

    private function _registerPlayerHtml()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator');
    }

    private function _fieldBuilder()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder');
    }

    private function _optionValidator()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator');
    }

    private function _odr()
    {
        $this->_expectRegistrationAndDefinition(
            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'
        );
    }

    private function _hrch()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler');
    }

    private function _hrps()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService');
    }

    private function _http()
    {
        $this->_expectRegistrationAndDefinition('ehough_shortstop_spi_HttpMessageParser', 'ehough_shortstop_impl_exec_DefaultHttpMessageParser');

        $transportClasses = array(

            'ehough_shortstop_impl_exec_command_ExtCommand',
            'ehough_shortstop_impl_exec_command_CurlCommand',
            'ehough_shortstop_impl_exec_command_StreamsCommand',
            'ehough_shortstop_impl_exec_command_FsockOpenCommand',
            'ehough_shortstop_impl_exec_command_FopenCommand'
        );

        $transportReferences = array();

        foreach ($transportClasses as $transportClass) {

            $def = $this->_expectRegistration($transportClass, $transportClass);
            $def = $this->_expectArgument($def, new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpMessageParser'));
            $def = $this->_expectArgument($def, new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

            $transportReferences[] = $def;
        }

        $transportChainDefinition = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $transportReferences);
        $transportChainDefinition->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transportChainDefinition->setFactoryMethod('buildChain');

        $this->_expectFactoryMethod(
            $this->_expectFactoryClass(
                $this->_expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_transportchain', $transportChainDefinition),
                'tubepress_impl_ioc_ChainRegistrar'
            ),
            'buildChain'
        );

        $contentDecoderCommands = array(

            'ehough_shortstop_impl_decoding_content_command_NativeGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_SimulatedGzipDecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1950DecompressingCommand',
            'ehough_shortstop_impl_decoding_content_command_NativeDeflateRfc1951DecompressingCommand',
        );

        $contentDecodingRefs = array();

        foreach ($contentDecoderCommands as $command) {

            $def = $this->_expectRegistration($command, $command);
            $contentDecodingRefs[] = $def;
        }

        $contentDecodingChainDef = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $contentDecodingRefs);
        $contentDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $contentDecodingChainDef->setFactoryMethod('buildChain');

        $this->_expectFactoryMethod(
            $this->_expectFactoryClass(
                $this->_expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain', $contentDecodingChainDef),
                'tubepress_impl_ioc_ChainRegistrar'
            ),
            'buildChain'
        );

        $def = $this->_expectArgument($this->_expectRegistration('ehough_shortstop_spi_HttpContentDecoder',
            'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain'),
            new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain'));

        $this->_expectDefinition('ehough_shortstop_impl_decoding_content_HttpContentDecodingChain', $def);

        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderRefs = array();

        foreach ($transferDecoderCommands as $command) {

            $def = $this->_expectRegistration($command, $command);
            $transferDecoderRefs[] = $def;
        }

        $transferDecodingChainDef = new tubepress_api_ioc_Definition('ehough_chaingang_api_Chain', $transferDecoderRefs);
        $transferDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transferDecodingChainDef->setFactoryMethod('buildChain');

        $this->_expectFactoryMethod(
            $this->_expectFactoryClass(
                $this->_expectDefinition('_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain', $transferDecodingChainDef),
                'tubepress_impl_ioc_ChainRegistrar'
            ),
            'buildChain'
        );

        $def = $this->_expectArgument($this->_expectRegistration('ehough_shortstop_spi_HttpTransferDecoder',
                'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain'),
            new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain'));

        $this->_expectDefinition('ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain', $def);

        $this->_expectRegistration('ehough_shortstop_impl_listeners_request_RequestLoggingListener', 'ehough_shortstop_impl_listeners_request_RequestLoggingListener');

        $this->_expectArgument(
            $this->_expectRegistration('ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener', 'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener'),
            new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'));

        $this->_expectArgument(
            $this->_expectArgument(
                $this->_expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-transfer',
                    'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'),
                new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpTransferDecoder')
            ),
            'Transfer'
        );

        $this->_expectArgument(
            $this->_expectArgument(
                $this->_expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-content',
                    'ehough_shortstop_impl_listeners_response_ResponseDecodingListener'),
                new tubepress_api_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder')
            ),
            'Content'
        );

        $this->_expectRegistration('ehough_shortstop_impl_listeners_response_ResponseLoggingListener', 'ehough_shortstop_impl_listeners_response_ResponseLoggingListener');

        $final = $this->_expectArgument(
            $this->_expectArgument(
                $this->_expectRegistration('ehough_shortstop_api_HttpClientInterface', 'ehough_shortstop_impl_DefaultHttpClient'),
                new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_)
            ),
            new tubepress_api_ioc_Reference('_ehough_shortstop_impl_DefaultHttpClient_transportchain')
        );

        $this->_expectDefinition('ehough_shortstop_impl_DefaultHttpClient', $final);
    }

    private function _filesystem()
    {
        $this->_expectRegistrationAndDefinition('ehough_filesystem_FilesystemInterface', 'ehough_filesystem_Filesystem');
    }

    private function _feedFetcher()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_feed_FeedFetcher::_, 'tubepress_impl_feed_CacheAwareFeedFetcher');
    }

    private function _executionContext()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_context_ExecutionContext::_, 'tubepress_impl_context_MemoryExecutionContext');
    }

    private function _embeddedGenerator()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator');
    }

    private function _cssAndJs()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_html_CssAndJsGenerator::_, 'tubepress_impl_html_DefaultCssAndJsGenerator');
    }

    private function _ajaxHandler()
    {
        $this->_expectRegistrationAndDefinition(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler');
    }

    private function _cacheService()
    {
        $this->_expectRegistration('tubepress_addons_core_impl_patterns_ioc_IocContainerExtension-_registerCacheService-builderServiceId', 'tubepress_addons_core_impl_patterns_ioc_FilesystemCacheBuilder');

        $def = new tubepress_api_ioc_Definition('ehough_stash_PoolInterface');
        $this->_expectDefinition('tubepress_addons_core_impl_patterns_ioc_IocContainerExtension-_registerCacheService-actualPoolServiceId', $def);

        $mockCacheDefinition = $this->_expectArgument(
            $this->_expectRegistration('ehough_stash_PoolInterface', 'tubepress_impl_cache_PoolDecorator'),
            new tubepress_api_ioc_Reference('tubepress_addons_core_impl_patterns_ioc_IocContainerExtension-_registerCacheService-actualPoolServiceId')
        );

        $this->_mockContainer->shouldReceive('setDefinition')->once()->with('tubepress_impl_cache_PoolDecorator', $mockCacheDefinition);
    }

    private function _expectRegistration($id, $class)
    {
        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_Definition');

        $this->_mockContainer->shouldReceive('register')->once()->with($id, $class)->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('getClass')->andReturn($class);

        return $mockDefinition;
    }

    private function _expectRegistrationAndDefinition($id, $class)
    {
        $mockDefinition = $this->_expectRegistration($id, $class);

        $this->_mockContainer->shouldReceive('setDefinition')->once()->with($class, $mockDefinition);

        return $mockDefinition;
    }

    private function _expectTag(ehough_mockery_mockery_MockInterface $mock, $tag)
    {
        $mock->shouldReceive('addTag')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($tag) {

            return $actual === $tag;
        }))->andReturn($mock);

        return $mock;
    }

    private function _expectFactoryClass(ehough_mockery_mockery_MockInterface $mock, $class)
    {
        $mock->shouldReceive('setFactoryClass')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($class) {

            return $actual === $class;
        }))->andReturn($mock);

        return $mock;
    }

    private function _expectFactoryMethod(ehough_mockery_mockery_MockInterface $mock, $method)
    {
        $mock->shouldReceive('setFactoryMethod')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($method) {

            return $actual === $method;
        }))->andReturn($mock);

        return $mock;
    }

    private function _expectFactoryService(ehough_mockery_mockery_MockInterface $mock, $service)
    {
        $mock->shouldReceive('setFactoryService')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($service) {

            echo "xxx";

            return $actual === $service;
        }))->andReturn($mock);

        return $mock;
    }

    private function _expectDefinition($id, tubepress_api_ioc_Definition $definition)
    {
        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_Definition');

        $this->_mockContainer->shouldReceive('setDefinition')->once()->with($id, ehough_mockery_Mockery::on(function ($actualDefinition) use ($definition) {

            return $actualDefinition instanceof tubepress_api_ioc_Definition
                && $actualDefinition->getClass() === $definition->getClass();

        }))->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('getClass')->andReturn($definition->getClass());

        return $mockDefinition;
    }

    private function _expectArgument(ehough_mockery_mockery_MockInterface $mock, $arg)
    {
        if ($arg instanceof tubepress_api_ioc_Reference || is_string($arg)) {

            $mock->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($arg) {

                return "$actual" === "$arg";
            }))->andReturn($mock);

        } else {

            throw new RuntimeException();
        }

        return $mock;
    }
}