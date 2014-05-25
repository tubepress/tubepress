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
 * @covers tubepress_core_impl_http_PrimaryAjaxHandler
 */
class tubepress_test_core_impl_http_PrimaryAjaxHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_http_PrimaryAjaxHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpResponseCodeService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_api_http_RequestParametersInterface::_);
        $this->_mockHttpResponseCodeService     = $this->mock(tubepress_core_api_http_ResponseCodeInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_core_impl_http_PrimaryAjaxHandler(

            $this->_mockLogger,
            $this->_mockHttpRequestParameterService,
            $this->_mockHttpResponseCodeService
        );
    }

    public function testNoAction()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('action')->andReturn(null);
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(400)->andReturn(400);

        $this->expectOutputString('Missing "action" parameter');

        $this->_sut->handle();
    }

    public function testFoundSuitableCommand()
    {
        $mockHandler = $this->mock(tubepress_core_api_http_AjaxCommandInterface::_);
        $mockHandler->shouldReceive('getName')->andReturn('action');
        $mockHandler->shouldReceive('handle')->once();

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('action')->andReturn('action');

        $this->_sut->setPluggableAjaxCommandHandlers(array($mockHandler));

        $this->_sut->handle();

        $this->assertTrue(true);
    }

    public function testHandleNoSuitableCommandHandler()
    {
        $mockHandler = $this->mock(tubepress_core_api_http_AjaxCommandInterface::_);
        $mockHandler->shouldReceive('getName')->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('action')->andReturn('action');
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(500)->andReturn(500);

        $this->_sut->setPluggableAjaxCommandHandlers(array($mockHandler));

        $this->_sut->handle();

        $this->assertTrue(true);
    }

    public function testHandleNoCommandHandlers()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('action')->andReturn('action');
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(500)->andReturn(500);

        $this->_sut->handle();

        $this->assertTrue(true);
    }
}

