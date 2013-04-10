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
class tubepress_plugins_core_impl_patterns_ioc_IocContainerExtensionTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension
     */
    private $_sut;

    /**
     * @var ehough_iconic_ContainerBuilder
     */
    private $_mockParentContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension();

        $this->_mockParentContainer = new ehough_iconic_ContainerBuilder();
    }

    public function testGetAlias()
    {
        $this->assertEquals('core', $this->_sut->getAlias());
    }

    public function testLoad()
    {
        $this->_sut->load($this->_mockParentContainer);

        foreach ($this->_getExpectedServices() as $service) {

            $definition = $this->_mockParentContainer->getDefinition($service->id);

            $this->assertNotNull($definition);

            $this->assertTrue($definition->getClass() === $service->type);

            if (isset($service->tag)) {

                $this->assertTrue($definition->hasTag($service->tag));
            }
        }

        foreach ($this->_getExpectedAliases() as $expectedAliasMap) {

            $aliasedService = $this->_mockParentContainer->get($expectedAliasMap[1]);
            $originalService = $this->_mockParentContainer->get($expectedAliasMap[0]);

            $this->assertSame($originalService, $aliasedService);
        }
    }

    private function _getExpectedAliases()
    {
        return array(

            array(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler'),
            array('ehough_stash_PoolInterface', 'ehough_stash_impl_PearCacheLiteCache'),
            array(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'),
            array('ehough_tickertape_EventDispatcherInterface', 'ehough_tickertape_impl_StandardEventDispatcher'),
            array(tubepress_spi_context_ExecutionContext::_, 'tubepress_impl_context_MemoryExecutionContext'),
            array('ehough_filesystem_FilesystemInterface', 'ehough_fimble_impl_StandardFilesystem'),
            array(tubepress_spi_feed_FeedFetcher::_, 'tubepress_impl_feed_CacheAwareFeedFetcher'),
            array(tubepress_spi_html_HeadHtmlGenerator::_, 'tubepress_impl_html_DefaultHeadHtmlGenerator'),
            array('ehough_shortstop_impl_DefaultHttpMessageParser', 'ehough_shortstop_impl_DefaultHttpMessageParser'),
            array('ehough_shortstop_api_HttpClientInterface', 'ehough_shortstop_impl_HttpClientChain'),
            array(tubepress_spi_http_HttpRequestParameterService::_, 'tubepress_impl_http_DefaultHttpRequestParameterService'),
            array(tubepress_spi_options_OptionDescriptorReference::_, 'tubepress_impl_options_DefaultOptionDescriptorReference'),
            array(tubepress_spi_options_OptionValidator::_, 'tubepress_impl_options_DefaultOptionValidator'),
            array(tubepress_spi_options_ui_FieldBuilder::_, 'tubepress_impl_options_ui_DefaultFieldBuilder'),
            array(tubepress_spi_player_PlayerHtmlGenerator::_, 'tubepress_impl_player_DefaultPlayerHtmlGenerator'),
            array(tubepress_spi_querystring_QueryStringService::_, 'tubepress_impl_querystring_SimpleQueryStringService'),
            array(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_, 'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'),
            array(tubepress_spi_shortcode_ShortcodeParser::_, 'tubepress_impl_shortcode_SimpleShortcodeParser'),
            array('ehough_contemplate_api_TemplateBuilder', 'ehough_contemplate_impl_SimpleTemplateBuilder'),
            array(tubepress_spi_theme_ThemeHandler::_, 'tubepress_impl_theme_SimpleThemeHandler'),
            array(tubepress_spi_collector_VideoCollector::_, 'tubepress_impl_collector_DefaultVideoCollector'),
        );
    }

    private function _getExpectedServices()
    {
        $map = array(

            array(tubepress_spi_http_AjaxHandler::_, 'tubepress_impl_http_DefaultAjaxHandler'),
            array('ehough_stash_PoolInterface', 'ehough_stash_impl_PearCacheLiteCache'),
            array(tubepress_spi_embedded_EmbeddedHtmlGenerator::_, 'tubepress_impl_embedded_DefaultEmbeddedPlayerHtmlGenerator'),
            array('ehough_tickertape_EventDispatcherInterface', 'ehough_tickertape_impl_StandardEventDispatcher'),
            array(tubepress_spi_context_ExecutionContext::_, 'tubepress_impl_context_MemoryExecutionContext'),
            array('ehough_filesystem_FilesystemInterface', 'ehough_fimble_impl_StandardFilesystem'),
            array(tubepress_spi_feed_FeedFetcher::_, 'tubepress_impl_feed_CacheAwareFeedFetcher'),
            array(tubepress_spi_html_HeadHtmlGenerator::_, 'tubepress_impl_html_DefaultHeadHtmlGenerator'),
            array('ehough_shortstop_impl_DefaultHttpMessageParser', 'ehough_shortstop_impl_DefaultHttpMessageParser'),
            array('ehough_shortstop_impl_transports_ExtHttpTransport', 'ehough_shortstop_impl_transports_ExtHttpTransport'),
            array('ehough_shortstop_impl_transports_CurlTransport', 'ehough_shortstop_impl_transports_CurlTransport'),
            array('ehough_shortstop_impl_transports_StreamsTransport', 'ehough_shortstop_impl_transports_StreamsTransport'),
            array('ehough_shortstop_impl_transports_FsockOpenTransport', 'ehough_shortstop_impl_transports_FsockOpenTransport'),
            array('ehough_shortstop_impl_transports_FopenTransport', 'ehough_shortstop_impl_transports_FopenTransport'),
            array('ehough_shortstop_impl_contentencoding_NativeGzipDecompressor', 'ehough_shortstop_impl_contentencoding_NativeGzipDecompressor'),
            array('ehough_shortstop_impl_contentencoding_SimulatedGzipDecompressor', 'ehough_shortstop_impl_contentencoding_SimulatedGzipDecompressor'),
            array('ehough_shortstop_impl_contentencoding_NativeDeflateRfc1950Decompressor', 'ehough_shortstop_impl_contentencoding_NativeDeflateRfc1950Decompressor'),
            array('ehough_shortstop_impl_contentencoding_NativeDeflateRfc1951Decompressor', 'ehough_shortstop_impl_contentencoding_NativeDeflateRfc1951Decompressor'),
            array('ehough_shortstop_impl_transferencoding_ChunkedTransferDecoder', 'ehough_shortstop_impl_transferencoding_ChunkedTransferDecoder'),
            array('ehough_shortstop_api_HttpClientInterface', 'ehough_shortstop_impl_HttpClientChain'),
            array(tubepress_spi_http_HttpRequestParameterService::_, 'tubepress_impl_http_DefaultHttpRequestParameterService'),
            array(tubepress_spi_options_OptionDescriptorReference::_, 'tubepress_impl_options_DefaultOptionDescriptorReference'),
            array(tubepress_spi_options_OptionValidator::_, 'tubepress_impl_options_DefaultOptionValidator'),
            array(tubepress_spi_options_ui_FieldBuilder::_, 'tubepress_impl_options_ui_DefaultFieldBuilder'),
            array(tubepress_spi_player_PlayerHtmlGenerator::_, 'tubepress_impl_player_DefaultPlayerHtmlGenerator'),
            array(tubepress_spi_querystring_QueryStringService::_, 'tubepress_impl_querystring_SimpleQueryStringService'),
            array(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_, 'tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator'),
            array(tubepress_spi_shortcode_ShortcodeParser::_, 'tubepress_impl_shortcode_SimpleShortcodeParser'),
            array('ehough_contemplate_api_TemplateBuilder', 'ehough_contemplate_impl_SimpleTemplateBuilder'),
            array(tubepress_spi_theme_ThemeHandler::_, 'tubepress_impl_theme_SimpleThemeHandler'),
            array(tubepress_spi_collector_VideoCollector::_, 'tubepress_impl_collector_DefaultVideoCollector'),
            array('tubepress_plugins_core_impl_options_ui_CoreOptionsPageParticipant', 'tubepress_plugins_core_impl_options_ui_CoreOptionsPageParticipant', tubepress_spi_options_ui_PluggableOptionsPageParticipant::_),
            array('tubepress_plugins_core_impl_options_ui_CorePluggableFieldBuilder', 'tubepress_plugins_core_impl_options_ui_CorePluggableFieldBuilder', tubepress_spi_options_ui_PluggableFieldBuilder::_),
            array('tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService', 'tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService', tubepress_spi_http_PluggableAjaxCommandService::_),
            array('tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_VimeoPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_VimeoPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_SoloPluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_SoloPluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_player_YouTubePluggablePlayerLocationService', 'tubepress_plugins_core_impl_player_YouTubePluggablePlayerLocationService', tubepress_spi_player_PluggablePlayerLocationService::_),
            array('tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService', 'tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService', tubepress_spi_shortcode_PluggableShortcodeHandlerService::_),
            array('tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService', 'tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService', tubepress_spi_shortcode_PluggableShortcodeHandlerService::_),
            array('tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService', 'tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService', tubepress_spi_shortcode_PluggableShortcodeHandlerService::_),
            array('tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService', 'tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService', tubepress_spi_shortcode_PluggableShortcodeHandlerService::_),
            array('tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService', 'tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService', tubepress_spi_shortcode_PluggableShortcodeHandlerService::_),
            array('tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi','tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi'),
            array('tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables', 'tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables'),
            array('tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs', 'tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs'),
            array('tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams', 'tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams'),
            array('tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables', 'tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables'),
            array('tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName', 'tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName'),
            array('tubepress_plugins_core_impl_filters_gallerytemplate_Pagination', 'tubepress_plugins_core_impl_filters_gallerytemplate_Pagination'),
            array('tubepress_plugins_core_impl_filters_gallerytemplate_Player', 'tubepress_plugins_core_impl_filters_gallerytemplate_Player'),
            array('tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta', 'tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta'),
            array('tubepress_plugins_core_impl_filters_playertemplate_CoreVariables', 'tubepress_plugins_core_impl_filters_playertemplate_CoreVariables'),
            array('tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic', 'tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic'),
            array('tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover', 'tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover'),
            array('tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables', 'tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables'),
            array('tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables', 'tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables'),
            array('tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta', 'tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta'),
            array('tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic', 'tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic'),
            array('tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter', 'tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter'),
            array('tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper', 'tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper'),
            array('tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist', 'tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist'),
            array('tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender', 'tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender'),
        );


        $toReturn = array();

        foreach ($map as $s) {

            $service = new stdClass();
            $service->id = $s[0];
            $service->type = $s[1];

            if (isset($s[2])) {

                $service->tag = $s[2];
            }

            $toReturn[] = $service;
        }

        return $toReturn;
    }
}