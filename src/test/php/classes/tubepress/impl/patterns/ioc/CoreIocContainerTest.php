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
class org_tubepress_impl_patterns_ioc_CoreIocContainerTest extends TubePressUnitTest
{
    private $_sut;

    function setUp()
    {
        $this->_sut = new tubepress_impl_patterns_ioc_CoreIocContainer();
    }

    function testBuildsNormally()
    {
        $this->assertNotNull($this->_sut);
    }

    function testServiceConstructions()
    {
        $toTest = array(

            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_AJAX_HANDLER                => tubepress_spi_http_AjaxHandler::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_BOOTSTRAPPER                => tubepress_spi_bootstrap_Bootstrapper::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_CACHE                       => 'ehough_stash_api_Cache',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EMBEDDED_HTML_GENERATOR     => tubepress_spi_embedded_EmbeddedHtmlGenerator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_ENVIRONMENT_DETECTOR        => tubepress_spi_environment_EnvironmentDetector::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EVENT_DISPATCHER            => 'ehough_tickertape_api_IEventDispatcher',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EXECUTION_CONTEXT           => tubepress_spi_context_ExecutionContext::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_INSPECTOR              => tubepress_spi_feed_FeedInspector::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_FETCHER                => tubepress_spi_feed_FeedFetcher::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM                  => 'ehough_fimble_api_Filesystem',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM_FINDER_FACTORY   => 'ehough_fimble_api_FinderFactory',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HEAD_HTML_GENERATOR         => tubepress_spi_html_HeadHtmlGenerator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_CLIENT                 => 'ehough_shortstop_api_HttpClient',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_RESPONSE_HANDLER       => 'ehough_shortstop_api_HttpResponseHandler',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_REQUEST_PARAMS         => tubepress_spi_http_HttpRequestParameterService::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_DESCRIPTOR_REFERENCE => tubepress_api_service_options_OptionDescriptorReference::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_VALIDATOR            => tubepress_spi_options_OptionValidator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTIONS_UI_FIELDBUILDER     => tubepress_spi_options_ui_FieldBuilder::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLAYER_HTML_GENERATOR       => tubepress_spi_player_PlayerHtmlGenerator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLUGIN_DISCOVER             => tubepress_spi_plugin_PluginDiscoverer::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLUGIN_REGISTRY             => tubepress_spi_plugin_PluginRegistry::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_QUERY_STRING_SERVICE        => tubepress_spi_querystring_QueryStringService::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_HTML_GENERATOR    => tubepress_spi_shortcode_ShortcodeHtmlGenerator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_PARSER            => tubepress_spi_shortcode_ShortcodeParser::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_TEMPLATE_BUILDER            => 'ehough_contemplate_api_TemplateBuilder',
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_THEME_HANDLER               => tubepress_spi_theme_ThemeHandler::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_URL_BUILDER                 => tubepress_spi_feed_UrlBuilder::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_FACTORY               => tubepress_spi_factory_VideoFactory::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER              => tubepress_spi_provider_Provider::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER_CALCULATOR   => tubepress_spi_provider_ProviderCalculator::_,
            tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER_REGISTRY     => tubepress_spi_provider_VideoProviderRegistry::_
        );

        foreach ($toTest as $key => $value) {

            $this->_testServiceBuilt($key, $value);
        }
    }

    private function _testServiceBuilt($id, $class)
    {
        $obj = $this->_sut->get($id);

        $this->assertTrue($obj instanceof $class, "Failed to build $id of type $class. Instead got " . gettype($obj) . var_export($obj, true));
    }


}