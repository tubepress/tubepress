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
        $gets = $this->getGetterArray();
        $keys = array_keys($this->getTestMap());
        $interfaces = array_values($this->getTestMap());

        for ($x = 0; $x < count($gets); $x++) {

            $mockIocContainer = Mockery::mock('ehough_iconic_api_IContainer');

            $mockService = Mockery::mock($interfaces[$x]);

            $mockIocContainer->shouldReceive('get')->once()->with($keys[$x])->andReturn($mockService);

            $getMethod = 'get' . $gets[$x];

            tubepress_impl_patterns_ioc_KernelServiceLocator::setCoreIocContainer($mockIocContainer);

            $result = tubepress_impl_patterns_ioc_KernelServiceLocator::$getMethod();

            $this->assertSame($result, $mockService);
        }
    }

    function testAllServicesNullByDefault()
    {
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getAjaxHandler());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getCacheService());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getEmbeddedHtmlGenerator());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getFeedFetcher());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystem());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystemFinderFactory());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getHeadHtmlGenerator());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpClient());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpResponseHandler());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionValidator());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getPlayerHtmlGenerator());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginDiscoverer());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginRegistry());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getQueryStringService());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeHtmlGenerator());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getShortcodeParser());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getTemplateBuilder());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler());
        $this->assertNull(tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoCollector());
    }

    private function getTestMap()
    {
        return array(

            tubepress_spi_const_patterns_ioc_ServiceIds::AJAX_HANDLER                 => tubepress_spi_http_AjaxHandler::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::CACHE                        => 'ehough_stash_api_Cache',
            tubepress_spi_const_patterns_ioc_ServiceIds::EMBEDDED_HTML_GENERATOR      => tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::ENVIRONMENT_DETECTOR         => tubepress_spi_environment_EnvironmentDetector::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::EVENT_DISPATCHER             => 'ehough_tickertape_api_IEventDispatcher',
            tubepress_spi_const_patterns_ioc_ServiceIds::EXECUTION_CONTEXT            => tubepress_spi_context_ExecutionContext::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::FEED_FETCHER                 => tubepress_spi_feed_FeedFetcher::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::FILESYSTEM                   => 'ehough_fimble_api_Filesystem',
            tubepress_spi_const_patterns_ioc_ServiceIds::FILESYSTEM_FINDER_FACTORY    => 'ehough_fimble_api_FinderFactory',
            tubepress_spi_const_patterns_ioc_ServiceIds::HEAD_HTML_GENERATOR          => tubepress_spi_html_HeadHtmlGenerator::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_CLIENT                  => 'ehough_shortstop_api_HttpClient',
            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_RESPONSE_HANDLER        => 'ehough_shortstop_api_HttpResponseHandler',
            tubepress_spi_const_patterns_ioc_ServiceIds::HTTP_REQUEST_PARAMS          => tubepress_spi_http_HttpRequestParameterService::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::OPTION_DESCRIPTOR_REFERENCE  => tubepress_spi_options_OptionDescriptorReference::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::OPTION_VALIDATOR             => tubepress_spi_options_OptionValidator::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::OPTIONS_UI_FIELDBUILDER      => tubepress_spi_options_ui_FieldBuilder::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::PLAYER_HTML_GENERATOR        => tubepress_spi_player_PlayerHtmlGenerator::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::PLUGIN_DISCOVER              => tubepress_spi_plugin_PluginDiscoverer::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::PLUGIN_REGISTRY              => tubepress_spi_plugin_PluginRegistry::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::QUERY_STRING_SERVICE         => tubepress_spi_querystring_QueryStringService::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::SERVICE_COLLECTIONS_REGISTRY => tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::SHORTCODE_HTML_GENERATOR     => tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::SHORTCODE_PARSER             => tubepress_spi_shortcode_ShortcodeParser::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::TEMPLATE_BUILDER             => 'ehough_contemplate_api_TemplateBuilder',
            tubepress_spi_const_patterns_ioc_ServiceIds::THEME_HANDLER                => tubepress_spi_theme_ThemeHandler::_,
            tubepress_spi_const_patterns_ioc_ServiceIds::VIDEO_COLLECTOR               => tubepress_spi_collector_VideoCollector::_
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
            'OptionDescriptorReference',
            'OptionValidator',
            'OptionsUiFieldBuilder',
            'PlayerHtmlGenerator',
            'PluginDiscoverer',
            'PluginRegistry',
            'QueryStringService',
            'ServiceCollectionsRegistry',
            'ShortcodeHtmlGenerator',
            'ShortcodeParser',
            'TemplateBuilder',
            'ThemeHandler',
            'VideoCollector',
        );
    }
}