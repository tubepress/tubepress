<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_patterns_sl_ServiceLocatorTest extends tubepress_test_TubePressUnitTest
{
    public function testLazyLookups()
    {
        $gets       = $this->getGetterArray();
        $keys       = $this->getTestMap();
        $interfaces = $this->getTestMap();

        for ($x = 0; $x < count($gets); $x++) {

            $mockIocContainer = ehough_mockery_Mockery::mock('ehough_iconic_ContainerInterface');

            $mockService = ehough_mockery_Mockery::mock($interfaces[$x]);

            $mockIocContainer->shouldReceive('get')->once()->with($keys[$x])->andReturn($mockService);

            $getMethod = 'get' . $gets[$x];

            tubepress_impl_patterns_sl_ServiceLocator::setBackingIconicContainer($mockIocContainer);

            $result = tubepress_impl_patterns_sl_ServiceLocator::$getMethod();

            $this->assertSame($result, $mockService);
        }
    }

    private function getTestMap()
    {
        return array(

            tubepress_spi_http_AjaxHandler::_,
            'ehough_stash_interfaces_PoolInterface',
            tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            tubepress_spi_environment_EnvironmentDetector::_,
            tubepress_api_event_EventDispatcherInterface::_,
            tubepress_spi_context_ExecutionContext::_,
            'ehough_filesystem_FilesystemInterface',
            'ehough_finder_FinderFactoryInterface',
            tubepress_spi_html_CssAndJsHtmlGeneratorInterface::_,
            tubepress_spi_http_HttpClientInterface::_,
            tubepress_spi_http_HttpRequestParameterService::_,
            tubepress_spi_http_ResponseCodeHandler::_,
            tubepress_spi_options_OptionProvider::_,
            tubepress_spi_player_PlayerHtmlGenerator::_,
            tubepress_spi_querystring_QueryStringService::_,
            tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            tubepress_spi_shortcode_ShortcodeParser::_,
            'ehough_contemplate_api_TemplateBuilder',
            tubepress_spi_theme_ThemeFinderInterface::_,
            tubepress_spi_theme_ThemeHandlerInterface::_,
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
            'FileSystem',
            'FileSystemFinderFactory',
            'CssAndJsHtmlGenerator',
            'HttpClient',
            'HttpRequestParameterService',
            'HttpResponseCodeHandler',
            'OptionProvider',
            'PlayerHtmlGenerator',
            'QueryStringService',
            'ShortcodeHtmlGenerator',
            'ShortcodeParser',
            'TemplateBuilder',
            'ThemeFinder',
            'ThemeHandler',
            'VideoCollector',
        );
    }
}