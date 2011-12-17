<?php

abstract class org_tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest extends TubePressUnitTest {

    private $_sut;

    private $_context;

    private $_response;

    private $_pc;

    function setUp()
    {
        parent::setUp();
        $this->_sut = $this->buildSut();

        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_context = new stdClass;
        $this->_context->response = $this->_response;
    }

    /**
     * @expectedException Exception
     */
    function testNoEntity()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pc = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $this->_pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn($this->getProviderName());

        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(200);
        $this->_response->shouldReceive('getEntity')->once()->andReturn(null);

        $this->_sut->execute($this->_context);
    }

    function testWrongProvider()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pc = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $this->_pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('something');

        $this->assertFalse($this->_sut->execute($this->_context));
    }

    protected function getMessage()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_pc = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $this->_pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn($this->getProviderName());

        $result = $this->_sut->execute($this->_context);

        $this->assertTrue($result);

        $message = $this->_context->messageToDisplay;

        $this->assertNotNull($message);

        return $message;
    }

    protected abstract function buildSut();

    protected function getContext()
    {
        return $this->_context;
    }

    protected function getResponse()
    {
        return $this->_response;
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    protected function getProviderCalculator()
    {
        return $this->_pc;
    }

    protected abstract function getProviderName();
}

