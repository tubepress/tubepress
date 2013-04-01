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
class tubepress_impl_patterns_sl_ServiceLocatorTest extends TubePressUnitTest
{
    function testLazyLookups()
    {
        $gets       = $this->getGetterArray();
        $keys       = $this->getTestMap();
        $interfaces = $this->getTestMap();

        for ($x = 0; $x < count($gets); $x++) {

            $mockIocContainer = Mockery::mock('ehough_iconic_api_IContainer');

            $mockService = Mockery::mock($interfaces[$x]);

            $mockIocContainer->shouldReceive('get')->once()->with($keys[$x])->andReturn($mockService);

            $getMethod = 'get' . $gets[$x];

            tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($mockIocContainer);

            $result = tubepress_impl_patterns_sl_ServiceLocator::$getMethod();

            $this->assertSame($result, $mockService);
        }
    }

    private function getTestMap()
    {
        return array(

            tubepress_spi_http_AjaxHandler::_,
            'ehough_stash_api_Cache',
            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            tubepress_spi_environment_EnvironmentDetector::_,
            'ehough_tickertape_api_IEventDispatcher',
            tubepress_spi_context_ExecutionContext::_,
            tubepress_spi_feed_FeedFetcher::_,
            'ehough_fimble_api_Filesystem',
            'ehough_fimble_api_FinderFactory',
            tubepress_spi_html_HeadHtmlGenerator::_,
            'ehough_shortstop_api_HttpClient',
            'ehough_shortstop_api_HttpResponseHandler',
            tubepress_spi_http_HttpRequestParameterService::_,
            'ehough_jameson_api_IDecoder',
            'ehough_jameson_api_IEncoder',
            tubepress_spi_options_OptionDescriptorReference::_,
            tubepress_spi_options_OptionValidator::_,
            tubepress_spi_options_ui_FieldBuilder::_,
            tubepress_spi_player_PlayerHtmlGenerator::_,
            tubepress_spi_plugin_PluginDiscoverer::_,
            tubepress_spi_plugin_PluginRegistry::_,
            tubepress_spi_querystring_QueryStringService::_,
            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            tubepress_spi_shortcode_ShortcodeParser::_,
            'ehough_contemplate_api_TemplateBuilder',
            tubepress_spi_theme_ThemeHandler::_,
            tubepress_spi_collector_VideoCollector::_,
        );
    }

    private function getGetterArray()
    {
        return array(

            'AjaxHandler',
            'CacheService',
            'EmbeddedHtmlGenerator',
            'EnvironmentDetector',
            'EventDispatcher',
            'ExecutionContext',
            'FeedFetcher',
            'FileSystem',
            'FileSystemFinderFactory',
            'HeadHtmlGenerator',
            'HttpClient',
            'HttpResponseHandler',
            'HttpRequestParameterService',
            'JsonDecoder',
            'JsonEncoder',
            'OptionDescriptorReference',
            'OptionValidator',
            'OptionsUiFieldBuilder',
            'PlayerHtmlGenerator',
            'PluginDiscoverer',
            'PluginRegistry',
            'QueryStringService',
            'ShortcodeHtmlGenerator',
            'ShortcodeParser',
            'TemplateBuilder',
            'ThemeHandler',
            'VideoCollector',
        );
    }
}