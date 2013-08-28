<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_http_DefaultAjaxHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_http_DefaultAjaxHandler
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

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_http_DefaultAjaxHandler();

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockHttpResponseCodeService     = $this->createMockSingletonService(tubepress_spi_http_ResponseCodeHandler::_);
    }

    public function testNoAction()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn(null);
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(400)->andReturn(400);

        $this->expectOutputString('Missing "action" parameter');

        $this->_sut->handle();
    }

    public function testFoundSuitableCommand()
    {
        $mockHandler = ehough_mockery_Mockery::mock(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('action');
        $mockHandler->shouldReceive('handle')->once();
        $mockHandler->shouldReceive('getHttpStatusCode')->once()->andReturn(300);
        $mockHandler->shouldReceive('getOutput')->once()->andReturn('hi');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(300)->andReturn(300);

        $this->_sut->setPluggableAjaxCommandHandlers(array($mockHandler));

        $this->expectOutputString('hi');

        $this->_sut->handle();
    }

    public function testHandleNoSuitableCommandHandler()
    {
        $mockHandler = ehough_mockery_Mockery::mock(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(500)->andReturn(500);

        $this->_sut->setPluggableAjaxCommandHandlers(array($mockHandler));

        $this->_sut->handle();

        $this->assertTrue(true);
    }

    public function testHandleNoCommandHandlers()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');
        $this->_mockHttpResponseCodeService->shouldReceive('setResponseCode')->once()->with(500)->andReturn(500);

        $this->_sut->handle();

        $this->assertTrue(true);
    }
}

