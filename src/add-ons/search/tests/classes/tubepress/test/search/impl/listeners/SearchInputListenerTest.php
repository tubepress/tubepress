<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_search_impl_listeners_SearchInputTemplateListener
 */
class tubepress_test_search_impl_listeners_SearchInputTemplateListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_search_impl_listeners_SearchInputTemplateListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockContext       = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockRequestParams = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockUrlFactory    = $this->mock(tubepress_api_url_UrlFactoryInterface::_);

        $this->_sut = new tubepress_search_impl_listeners_SearchInputTemplateListener(
            $this->_mockContext,
            $this->_mockUrlFactory,
            $this->_mockRequestParams);
    }

    public function testYouTubeFavorites()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SEARCH_RESULTS_URL)->andReturn('');
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');
        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $mockUrl->shouldReceive('toString')->once()->andReturn('abcabc');
        $mockQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $mockQuery->shouldReceive('remove')->once()->with('tubepress_search');
        $mockQuery->shouldReceive('toArray')->once()->andReturn(array('foo' => 'bar', 'something' => 'else'));
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($mockUrl);

        $this->_mockRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_search')->andReturn("search for something");

        $expected = array(
            'foo'                                                          => 'bar',
            tubepress_api_template_VariableNames::SEARCH_HANDLER_URL   => 'abcabc',
            tubepress_api_template_VariableNames::SEARCH_HIDDEN_INPUTS => array('foo' => 'bar', 'something' => 'else'),
            tubepress_api_template_VariableNames::SEARCH_TERMS         => 'search for something'
        );

        $event = $this->mock('tubepress_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $event->shouldReceive('setSubject')->once()->with($expected);

        $this->_sut->onSearchInputTemplatePreRender($event);

        $this->assertTrue(true);
    }
}