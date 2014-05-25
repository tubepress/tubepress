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
 * @covers tubepress_core_impl_listeners_html_generation_SearchOutputListener
 */
class tubepress_test_core_impl_shortcode_SearchOutputCommandTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_html_generation_SearchOutputListener
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

        $this->_mockExecutionContext             = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockThumbGalleryShortcodeHandler = $this->mock('tubepress_core_impl_listeners_html_generation_ThumbGalleryListener');
        $this->_mockHttpRequestParameterService  = $this->mock(tubepress_core_api_http_RequestParametersInterface::_);
        $this->_mockLogger                       = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEvent                        = $this->mock('tubepress_core_api_event_EventInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_core_impl_listeners_html_generation_SearchOutputListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockThumbGalleryShortcodeHandler,
            $this->_mockHttpRequestParameterService
        );
    }

    public function testCantExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::OUTPUT)->andReturn(tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_INPUT);

        $this->_sut->onHtmlGeneration($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testExecuteYouTube()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::OUTPUT)->andReturn(tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_PROVIDER)->andReturn('youtube');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_core_api_const_options_Names::GALLERY_SOURCE, tubepress_youtube_api_const_options_Values::YOUTUBE_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with(tubepress_core_api_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $this->_mockThumbGalleryShortcodeHandler->shouldReceive('onHtmlGeneration')->once()->with($this->_mockEvent);

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteVimeo()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::OUTPUT)->andReturn(tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_PROVIDER)->andReturn('vimeo');
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_core_api_const_options_Names::GALLERY_SOURCE, tubepress_vimeo_api_const_options_Values::VIMEO_SEARCH);
        $this->_mockExecutionContext->shouldReceive('set')->once()->with(tubepress_vimeo_api_const_options_Names::VIMEO_SEARCH_VALUE, "(#@@!!search (())(())((terms*$$#")->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with(tubepress_core_api_const_http_ParamName::SEARCH_TERMS)->andReturn("(#@@!!search (())(())((terms*$$#");

        $this->_mockThumbGalleryShortcodeHandler->shouldReceive('onHtmlGeneration')->once()->with($this->_mockEvent);

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);

    }

    public function testExecuteHasToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::OUTPUT)->andReturn(tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY)->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->twice()->with(tubepress_core_api_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('');
        $this->_mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteDoesntHaveToShowSearchResultsNotSearching()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::OUTPUT)->andReturn(tubepress_core_api_const_options_ValidValues::OUTPUT_SEARCH_RESULTS);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY)->andReturn(false);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->atLeast(1)->with(tubepress_core_api_const_http_ParamName::SEARCH_TERMS)->andReturn("");

        $this->_sut->onHtmlGeneration($this->_mockEvent);
        $this->assertTrue(true);

    }
}
