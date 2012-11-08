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
class tubepress_impl_patterns_ioc_KernelServiceLocatorTest extends TubePressUnitTest
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

            tubepress_impl_patterns_ioc_KernelServiceLocator::setIocContainer($mockIocContainer);

            $result = tubepress_impl_patterns_ioc_KernelServiceLocator::$getMethod();

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