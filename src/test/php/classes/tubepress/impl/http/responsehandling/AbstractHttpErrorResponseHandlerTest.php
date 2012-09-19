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
abstract class tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockContext;

    private $_mockResponse;

    private $_mockProviderCalculator;

    function setUp()
    {
        $this->_sut = $this->buildSut();

        $this->_mockResponse = new ehough_shortstop_api_HttpResponse();
        $this->_mockContext  = new ehough_chaingang_impl_StandardContext();
        $this->_mockContext->put(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_RESPONSE, $this->_mockResponse);

        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
    }

    /**
     * @expectedException RuntimeException
     */
    function testNoEntity()
    {
        $this->_mockResponse->setStatusCode(200);

        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn($this->getProviderName());

        $this->_sut->execute($this->_mockContext);
    }

    function testWrongProvider()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('something');

        $this->assertFalse($this->_sut->execute($this->_mockContext));
    }

    protected function getMessage()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn($this->getProviderName());

        $result = $this->_sut->execute($this->_mockContext);

        $this->assertTrue($result);

        $message = $this->_mockContext->get(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_ERROR_MESSAGE);

        $this->assertNotNull($message);

        return $message;
    }

    protected abstract function buildSut();

    protected function getContext()
    {
        return $this->_mockContext;
    }

    protected function getResponse()
    {
        return $this->_mockResponse;
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    protected function getProviderCalculator()
    {
        return $this->_mockProviderCalculator;
    }

    protected abstract function getProviderName();
}

