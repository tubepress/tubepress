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
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER)->andReturn(org_tubepress_api_provider_Provider::VIMEO);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE, "search terms");

        $qss            = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $qss->shouldReceive('getSearchTerms')->once()->andReturn("(#@@!!search (())(())((terms*$$#");

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
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER)->andReturn(org_tubepress_api_provider_Provider::YOUTUBE);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::MODE, org_tubepress_api_const_options_values_ModeValue::TAG);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::TAG_VALUE, "search terms");

        $qss            = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $qss->shouldReceive('getSearchTerms')->once()->andReturn("(#@@!!search (())(())((terms*$$#");

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
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY)->andReturn(true);

        $qss            = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $qss->shouldReceive('getSearchTerms')->once()->andReturn("");

        $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('', $mockChainContext->returnValue);
    }

    function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY)->andReturn(false);

        $qss            = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $qss->shouldReceive('getSearchTerms')->once()->andReturn("");

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }
}