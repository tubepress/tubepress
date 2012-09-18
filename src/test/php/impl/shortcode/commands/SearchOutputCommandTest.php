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
class org_tubepress_impl_shortcode_commands_SearchOutputCommandTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockChain;

    private $_mockExecutionContext;

    private $_mockHttpRequestParameterService;

    function setup()
    {
        $this->_mockChain = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_impl_shortcode_commands_SearchOutputCommand($this->_mockChain);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
    }

    function tearDown()
    {
        Mockery::close();
    }

    function testCantExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_INPUT);

        $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
    }

    function testExecuteVimeo()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(tubepress_spi_provider_Provider::VIMEO);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

        $this->_mockChain->shouldReceive('execute')->once()->with($mockChainContext)->andReturn(true);

        $this->assertTrue($this->_sut->execute($mockChainContext));
    }

    function testExecuteVimeoExecContextSetFails()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(tubepress_spi_provider_Provider::VIMEO);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testExecuteYouTubeExecContextSetFails()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(tubepress_spi_provider_Provider::YOUTUBE);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testExecuteYouTube()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(tubepress_spi_provider_Provider::YOUTUBE);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

        $this->_mockChain->shouldReceive('execute')->once()->with($mockChainContext)->andReturn(true);

        $this->assertTrue($this->_sut->execute($mockChainContext));
    }

    function testExecuteHasToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $mockChainContext = new ehough_chaingang_impl_StandardContext();

        $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('', $mockChainContext->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));
    }

    function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
    }
}
