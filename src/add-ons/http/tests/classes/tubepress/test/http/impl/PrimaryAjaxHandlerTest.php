<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_impl_PrimaryAjaxHandler
 */
class tubepress_test_http_impl_PrimaryAjaxHandlerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_impl_PrimaryAjaxHandler
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpResponseCodeService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockHttpResponseCodeService     = $this->mock(tubepress_api_http_ResponseCodeInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockTemplating                  = $this->mock(tubepress_api_template_TemplatingInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_http_impl_PrimaryAjaxHandler(

            $this->_mockLogger,
            $this->_mockHttpRequestParameterService,
            $this->_mockHttpResponseCodeService,
            $this->_mockEventDispatcher,
            $this->_mockTemplating
        );
    }

    public function testNoAction()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_action')->andReturn(false);

        $this->_setupForError();
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(400)->andReturn(400);

        $this->expectOutputString('foobar');

        $this->_sut->handle();
    }

    public function testFoundSuitableCommand()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_action')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_action')->andReturn('xyz');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::HTTP_AJAX . '.xyz', $mockEvent);

        $mockEvent->shouldReceive('getArguments')->once()->andReturn(array('handled' => true));

        $this->_sut->handle();

        $this->assertTrue(true);
    }

    public function testNoFoundSuitableCommand()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_action')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_action')->andReturn('xyz');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::HTTP_AJAX . '.xyz', $mockEvent);

        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(400)->andReturn(400);

        $mockEvent->shouldReceive('getArguments')->once()->andReturn(array('handled' => false));

        $this->expectOutputString('foobar');

        $this->_setupForError();
        $this->_sut->handle();

        $this->assertTrue(true);
    }

    public function testInternalError()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_action')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_action')->andReturn('xyz');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::HTTP_AJAX . '.xyz', $mockEvent)->andThrow(new RuntimeException('hi'));

        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(500)->andReturn(500);

        $this->expectOutputString('foobar');

        $this->_setupForError();
        $this->_sut->handle();

        $this->assertTrue(true);
    }

    private function _setupForError()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()
            ->with(Mockery::type('RuntimeException'))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::HTML_EXCEPTION_CAUGHT,
            $mockEvent);
        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('exception/ajax', Mockery::type('array'))
            ->andReturn('foobar');
    }
}
