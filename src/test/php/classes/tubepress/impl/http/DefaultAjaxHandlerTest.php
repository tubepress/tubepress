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
class org_tubepress_impl_http_DefaultAjaxHandlerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_http_DefaultAjaxHandler
     */
    private $_sut;

    private $_mockHttpRequestParameterService;

    private $_mockServiceCollectionsRegistry;

    function setup()
    {
        $this->_sut = new tubepress_impl_http_DefaultAjaxHandler();

        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockServiceCollectionsRegistry  = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
    }

    function testNoAction()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn(null);

        $this->expectOutputString('Missing "action" parameter');

        $this->_sut->handle();

        $this->assertTrue(400 === http_response_code());
    }

    function testFoundSuitableCommand()
    {
        $mockHandler = Mockery::mock(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('action');
        $mockHandler->shouldReceive('handle')->once();
        $mockHandler->shouldReceive('getHttpStatusCode')->once()->andReturn(300);
        $mockHandler->shouldReceive('getOutput')->once()->andReturn('hi');

        $mockHandlers = array($mockHandler);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_http_PluggableAjaxCommandService::_)->andReturn($mockHandlers);

        $this->expectOutputString('hi');

        $this->_sut->handle();

        $this->assertTrue(300 === http_response_code());

    }

    function testHandleNoSuitableCommandHandler()
    {
        $mockHandler = Mockery::mock(tubepress_spi_http_PluggableAjaxCommandService::_);
        $mockHandler->shouldReceive('getName')->andReturn('x');

        $mockHandlers = array($mockHandler);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_http_PluggableAjaxCommandService::_)->andReturn($mockHandlers);

        $this->_sut->handle();

        $this->assertTrue(500 === http_response_code());
    }

    function testHandleNoCommandHandlers()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_spi_const_http_ParamName::ACTION)->andReturn('action');

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_http_PluggableAjaxCommandService::_)->andReturn(array());

        $this->_sut->handle();

        $this->assertTrue(500 === http_response_code());
    }

    function testSetHttpStatusCode()
    {
        tubepress_impl_http_DefaultAjaxHandler::simulatedHttpResponseCode(505);

        $this->assertTrue(505 === http_response_code());
    }

    function testGetHttpStatusCode()
    {
        $this->assertTrue(200 === tubepress_impl_http_DefaultAjaxHandler::simulatedHttpResponseCode());
    }
}

