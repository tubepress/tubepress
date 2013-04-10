<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_http_DefaultAjaxHandlerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_http_DefaultAjaxHandler
     */
    private $_sut;

    private $_mockHttpRequestParameterService;

    function onSetup()
    {
        $this->_sut = new tubepress_impl_http_DefaultAjaxHandler();

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
    }

    /**
     * @runInSeparateProcess
     */
    function testNoAction()
    {
        //$this->setInIsolation(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn(null);

        $this->expectOutputString('Missing "action" parameter');

        $this->_sut->handle();

        $actualResponse = http_response_code();

        $this->assertEquals(400, $actualResponse);
    }

    /**
     * @runInSeparateProcess
     */
    function testFoundSuitableCommand()
    {
        $this->setInIsolation(true);

        $mockHandler = $this->createMockPluggableService(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('action');
        $mockHandler->shouldReceive('handle')->once();
        $mockHandler->shouldReceive('getHttpStatusCode')->once()->andReturn(300);
        $mockHandler->shouldReceive('getOutput')->once()->andReturn('hi');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->expectOutputString('hi');

        $this->_sut->handle();

        $this->assertTrue(300 === http_response_code());

    }

    /**
     * @runInSeparateProcess
     */
    function testHandleNoSuitableCommandHandler()
    {
        $this->setInIsolation(true);

        $mockHandler = $this->createMockPluggableService(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('x');

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->_sut->handle();

        $this->assertTrue(500 === http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    function testHandleNoCommandHandlers()
    {
        $this->setInIsolation(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->_sut->handle();

        $this->assertTrue(500 === http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    function testSetHttpStatusCode()
    {
        $this->setInIsolation(true);

        tubepress_impl_http_DefaultAjaxHandler::simulatedHttpResponseCode(505);

        $this->assertTrue(505 === http_response_code());
    }

    function testGetHttpStatusCode()
    {
        $this->assertTrue(200 === tubepress_impl_http_DefaultAjaxHandler::simulatedHttpResponseCode());
    }
}

