<?php

require_once BASE . '/sys/classes/org/tubepress/impl/shortcode/commands/SearchOutputCommand.class.php';

class org_tubepress_impl_shortcode_commands_SearchOutputCommandTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_commands_SearchOutputCommand();
    }

    function testCantExecute()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT);

        $this->assertFalse($this->_sut->execute(new stdClass()));
    }

    function testExecuteVimeo()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(org_tubepress_api_provider_Provider::VIMEO);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE, org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $qss            = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $qss->shouldReceive('getParamValue')->once()->with(org_tubepress_api_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array('org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'))->andReturn(true);

        $this->assertTrue($this->_sut->execute($mockChainContext));
    }

    function testExecuteYouTube()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER)->andReturn(org_tubepress_api_provider_Provider::YOUTUBE);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE, org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $qss            = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $qss->shouldReceive('getParamValue')->once()->with(org_tubepress_api_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array('org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'))->andReturn(true);

        $this->assertTrue($this->_sut->execute($mockChainContext));
    }

    function testExecuteHasToShowSearchResultsNotSearching()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(true);

        $qss            = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $qss->shouldReceive('getParamValue')->once()->with(org_tubepress_api_const_http_ParamName::SEARCH_TERMS)->andReturn("");


        $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('', $mockChainContext->returnValue);
    }

    function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY)->andReturn(false);

        $qss            = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $qss->shouldReceive('getParamValue')->once()->with(org_tubepress_api_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }
}
