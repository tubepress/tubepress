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

/**
 * @covers tubepress_core_html_search_impl_listeners_html_SearchOutputListener
 */
class tubepress_test_core_html_search_impl_listeners_html_SearchOutputCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_search_impl_listeners_html_SearchOutputListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThumbGalleryShortcodeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {

        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEvent                       = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_html_search_impl_listeners_html_SearchOutputListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockHttpRequestParameterService
        );
    }

    public function testCantExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_html_search_api_Constants::OUTPUT_SEARCH_INPUT);

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testExecuteYouTube()
    {
        $mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockMediaProvider->shouldReceive('getName')->once()->andReturn('youtube');
        $mockMediaProvider->shouldReceive('getSearchModeName')->once()->andReturn('search');
        $mockMediaProvider->shouldReceive('getSearchQueryOptionName')->once()->andReturn('searchValue');
        $this->_sut->setMediaProviders(array($mockMediaProvider));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_html_search_api_Constants::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_search_api_Constants::OPTION_SEARCH_PROVIDER)->andReturn('youtube');
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_GALLERY_SOURCE, 'search');
        $this->_mockExecutionContext->shouldReceive('setEphemeralOption')->once()->with('searchValue', "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with(tubepress_core_http_api_Constants::PARAM_NAME_SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteHasToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_html_search_api_Constants::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with(tubepress_core_http_api_Constants::PARAM_NAME_SEARCH_TERMS)->andReturn("");

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('');
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_OUTPUT)->andReturn(tubepress_core_html_search_api_Constants::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_search_api_Constants::OPTION_SEARCH_RESULTS_ONLY)->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->atLeast(1)->with(tubepress_core_http_api_Constants::PARAM_NAME_SEARCH_TERMS)->andReturn("");

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);

    }
}
