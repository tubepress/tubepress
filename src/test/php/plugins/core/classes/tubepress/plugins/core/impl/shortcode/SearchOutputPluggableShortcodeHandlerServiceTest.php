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
class tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerServiceTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService
     */
    private $_sut;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    private $_mockThumbGalleryShortcodeHandler;

    function setup()
    {

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockThumbGalleryShortcodeHandler = Mockery::mock(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService($this->_mockThumbGalleryShortcodeHandler);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
    }


    function testCantExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_INPUT);

        $this->assertFalse($this->_sut->shouldExecute());
    }

    function testExecuteVimeo()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn('vimeo');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $this->_mockThumbGalleryShortcodeHandler->shouldReceive('getHtml')->once()->andReturn('foobar');

        $this->assertEquals('foobar', $this->_sut->getHtml());
    }


    function testExecuteYouTube()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn('youtube');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $this->_mockThumbGalleryShortcodeHandler->shouldReceive('getHtml')->once()->andReturn('xyz');

        $this->assertEquals('xyz', $this->_sut->getHtml());
    }

    function testExecuteHasToShowSearchResultsNotSearching()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->assertEquals('', $this->_sut->getHtml());
    }

    function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->assertFalse($this->_sut->shouldExecute());
    }
}
