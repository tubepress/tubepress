<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_core_impl_ioc_IocContainerExtension<extended>
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
        $this->_optionProvider();
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
    }

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_ioc_IocContainerExtension();
    }

    private function _listeners()
    {
        $listeners = array(

            'tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJs' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_EMBEDDED, 'method' => 'onEmbeddedHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED, 'method' => 'onEmbeddedTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION, 'method' => 'onPlayerTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_options_PreValidationOptionSetStringMagic' =>
                array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET, 'method' => 'onPreValidationOptionSet', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT, 'method' => 'onSearchInputTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_SingleVideoMeta' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO, 'method' => 'onSingleVideoTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs' =>
                array('event' => tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY, 'method' => 'onGalleryHtml', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10400),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10300),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10200),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta' =>
                array('event' => tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY, 'method' => 'onGalleryTemplate', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_options_ExternalInputStringMagic' =>
                array('event' => tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL, 'method' => 'onIncomingInput', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10000),

            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10100),

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10200),

            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender' =>
                array('event' => tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE, 'method' => 'onVideoGalleryPage', 'priority' => 10300),

            'tubepress_addons_core_impl_listeners_html_JsConfig' =>
                array('event' => tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS, 'method' => 'onInlineJs', 'priority' => 10000)
        );

        foreach ($listeners as $className => $tagAttributes) {

            $this->expectRegistration($className, $className)->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, $tagAttributes);
        }

        $this->expectRegistration(

            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams',
            'tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams'
            )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT, 'method' => 'onGalleryInitJs', 'priority' => 10000))
             ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));
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
            ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService'));

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService')
             ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_)
             ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService'));

        $this->expectRegistration('tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService', 'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService')
            ->withTag(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);

        $this->expectRegistration('tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder', 'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder')
            ->withTag(tubepress_spi_options_ui_PluggableFieldBuilder::_)
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));

    }

    private function _videoCollector()
    {
        $this->expectRegistration(tubepress_spi_collector_VideoCollector::_,
            'tubepress_impl_collector_DefaultVideoCollector')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));

    }

    private function _themeHandler()
    {
        $this->expectRegistration(tubepress_spi_theme_ThemeHandler::_,
            'tubepress_impl_theme_SimpleThemeHandler');
    }

    private function _templateBuilder()
    {
        $this->expectRegistration('ehough_contemplate_api_TemplateBuilder',
            'ehough_contemplate_impl_SimpleTemplateBuilder');
    }

    private function _shortcodeParser()
    {
        $this->expectRegistration(tubepress_spi_shortcode_ShortcodeParser::_,
            'tubepress_impl_shortcode_SimpleShortcodeParser');
    }

    private function _shortcode()
    {
        $this->expectRegistration(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator')->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_shortcode_PluggableShortcodeHandlerService::_, 'method' => 'setPluggableShortcodeHandlers'));
    }

    private function _qss()
    {
        $this->expectRegistration(tubepress_spi_querystring_QueryStringService::_,
            'tubepress_impl_querystring_SimpleQueryStringService');
    }

    private function _registerPlayerHtml()
    {
        $this->expectRegistration(tubepress_spi_player_PlayerHtmlGenerator::_,
            'tubepress_impl_player_DefaultPlayerHtmlGenerator')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'));

    }

    private function _optionProvider()
    {
        $this->expectRegistration(
            'tubepress_addons_core_impl_options_CoreOptionsProvider',
            'tubepress_addons_core_impl_options_CoreOptionsProvider'
        )->withTag(tubepress_spi_options_PluggableOptionDescriptorProvider::_)
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_player_PluggablePlayerLocationService::_, 'method' => 'setPluggablePlayerLocations'))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'));
    }

    private function _fieldBuilder()
    {
        $this->expectRegistration(tubepress_spi_options_ui_FieldBuilder::_,
            'tubepress_impl_options_ui_DefaultFieldBuilder')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                array('tag' => tubepress_spi_options_ui_PluggableFieldBuilder::_, 'method' => 'setPluggableFieldBuilders'));

    }

    private function _optionValidator()
    {
        $this->expectRegistration(tubepress_spi_options_OptionValidator::_,
            'tubepress_impl_options_DefaultOptionValidator');
    }

    private function _odr()
    {
        $this->expectRegistration(

            tubepress_spi_options_OptionDescriptorReference::_,
            'tubepress_impl_options_DefaultOptionDescriptorReference'

        )->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_options_PluggableOptionDescriptorProvider::_, 'method' => 'setPluggableOptionDescriptorProviders'));
    }

    private function _hrch()
    {
        $this->expectRegistration(tubepress_spi_http_ResponseCodeHandler::_,
            'tubepress_impl_http_DefaultResponseCodeHandler');
    }

    private function _hrps()
    {
        $this->expectRegistration(tubepress_spi_http_HttpRequestParameterService::_,
            'tubepress_impl_http_DefaultHttpRequestParameterService');
    }

    private function _http()
    {
        $this->expectRegistration('ehough_shortstop_spi_HttpMessageParser', 'ehough_shortstop_impl_exec_DefaultHttpMessageParser');

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
                ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpMessageParser'))
                ->withArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));

            $transportReferences[] = $def;
        }

        $transportChainDefinition = new tubepress_impl_ioc_Definition('ehough_chaingang_api_Chain', $transportReferences);
        $transportChainDefinition->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transportChainDefinition->setFactoryMethod('buildChain');

        $this->expectDefinition('ehough_shortstop_impl_DefaultHttpClient_transportchain', $transportChainDefinition)
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

        $contentDecodingChainDef = new tubepress_impl_ioc_Definition('ehough_chaingang_api_Chain', $contentDecodingRefs);
        $contentDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $contentDecodingChainDef->setFactoryMethod('buildChain');

        $this->expectDefinition('ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain', $contentDecodingChainDef)
            ->withFactoryClass('tubepress_impl_ioc_ChainRegistrar')
            ->withFactoryMethod('buildChain');

        $this->expectRegistration('ehough_shortstop_spi_HttpContentDecoder', 'ehough_shortstop_impl_decoding_content_HttpContentDecodingChain')
            ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_impl_DefaultHttpClient_contentdecoderchain'));


        $transferDecoderCommands = array(

            'ehough_shortstop_impl_decoding_transfer_command_ChunkedTransferDecodingCommand'
        );

        $transferDecoderRefs = array();

        foreach ($transferDecoderCommands as $command) {

            $def = $this->expectRegistration($command, $command);
            $transferDecoderRefs[] = $def;
        }

        $transferDecodingChainDef = new tubepress_impl_ioc_Definition('ehough_chaingang_api_Chain', $transferDecoderRefs);
        $transferDecodingChainDef->setFactoryClass('tubepress_impl_ioc_ChainRegistrar');
        $transferDecodingChainDef->setFactoryMethod('buildChain');

        $this->expectDefinition('ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain', $transferDecodingChainDef)
            ->withFactoryClass('tubepress_impl_ioc_ChainRegistrar')
            ->withFactoryMethod('buildChain');

        $this->expectRegistration('ehough_shortstop_spi_HttpTransferDecoder', 'ehough_shortstop_impl_decoding_transfer_HttpTransferDecodingChain')
            ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_impl_DefaultHttpClient_transferdecoderchain'));

        $this->expectRegistration('ehough_shortstop_impl_listeners_request_RequestLoggingListener', 'ehough_shortstop_impl_listeners_request_RequestLoggingListener')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onPreRequest', 'priority' => 5000));

        $this->expectRegistration('ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener', 'ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener')
            ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::REQUEST, 'method' => 'onPreRequest', 'priority' => 10000));

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener__transfer',
                    'ehough_shortstop_impl_listeners_response_ResponseDecodingListener')
            ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpTransferDecoder'))
            ->withArgument('Transfer')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10300));

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseDecodingListener__content',
            'ehough_shortstop_impl_listeners_response_ResponseDecodingListener')
            ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_spi_HttpContentDecoder'))
            ->withArgument('Content')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10200));

        $this->expectRegistration('ehough_shortstop_impl_listeners_response_ResponseLoggingListener', 'ehough_shortstop_impl_listeners_response_ResponseLoggingListener')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_EVENT_LISTENER, array('event' => ehough_shortstop_api_Events::RESPONSE, 'method' => 'onResponse', 'priority' => 10100));

        $this->expectRegistration('ehough_shortstop_api_HttpClientInterface', 'ehough_shortstop_impl_DefaultHttpClient')
             ->withArgument(new tubepress_impl_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'))
             ->withArgument(new tubepress_impl_ioc_Reference('ehough_shortstop_impl_DefaultHttpClient_transportchain'));
    }

    private function _filesystem()
    {
        $this->expectRegistration('ehough_filesystem_FilesystemInterface', 'ehough_filesystem_Filesystem');
    }

    private function _feedFetcher()
    {
        $this->expectRegistration(tubepress_spi_feed_FeedFetcher::_, 'tubepress_impl_feed_CacheAwareFeedFetcher');
    }

    private function _executionContext()
    {
        $this->expectRegistration(tubepress_spi_context_ExecutionContext::_, 'tubepress_impl_context_MemoryExecutionContext');
    }

    private function _embeddedGenerator()
    {
        $this->expectRegistration(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_provider_PluggableVideoProviderService::_, 'method' => 'setPluggableVideoProviders'))
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER, array('tag' => tubepress_spi_embedded_PluggableEmbeddedPlayerService::_, 'method' => 'setPluggableEmbeddedPlayers'));

    }

    private function _cssAndJs()
    {
        $this->expectRegistration(tubepress_spi_html_CssAndJsGenerator::_, 'tubepress_impl_html_DefaultCssAndJsGenerator');
    }

    private function _ajaxHandler()
    {
         $this->expectRegistration(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler')
            ->withTag(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER,
                 array('tag' => tubepress_spi_http_PluggableAjaxCommandService::_, 'method' => 'setPluggableAjaxCommandHandlers'));
    }

    private function _cacheService()
    {
        $this->expectRegistration('tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_builderServiceId', 'tubepress_addons_core_impl_ioc_FilesystemCacheBuilder');

        $def = new tubepress_impl_ioc_Definition('ehough_stash_PoolInterface');
        $this->expectDefinition('tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_actualPoolServiceId', $def);

        $this->expectRegistration('ehough_stash_PoolInterface', 'tubepress_impl_cache_PoolDecorator')
            ->withArgument(new tubepress_impl_ioc_Reference('tubepress_addons_core_impl_ioc_IocContainerExtension__registerCacheService_actualPoolServiceId'))
            ;
    }
}