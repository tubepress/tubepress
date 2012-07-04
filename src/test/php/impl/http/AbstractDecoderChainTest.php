<?php

abstract class org_tubepress_impl_http_AbstractDecoderChainTest extends TubePressUnitTest {

    private $_sut;

    private $_response;

    private $_entity;

    function setup()
    {
        parent::setUp();
        $this->_sut = $this->buildSut();
        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
    }

    /**
     * @expectedException Exception
     */
    function testCannotDecode()
    {
        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass;
        $context->decoded = 'decoded';

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('createContextInstance')->once()->andReturn($context);
        $chain->shouldReceive('execute')->once()->with($context, $this->getArrayOfCommands())->andReturn(false);

        $this->_sut->decode($this->_response);
    }

    function testDecode()
    {
        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass;
        $context->decoded = 'decoded';

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('createContextInstance')->once()->andReturn($context);
        $chain->shouldReceive('execute')->once()->with($context, $this->getArrayOfCommands())->andReturn(true);

        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_TYPE)->andReturn('fooey');
        $this->_response->shouldReceive('setEntity')->once()->with($this->_entity);

        $this->_entity->shouldReceive('setContent')->once()->with('decoded');
        $this->_entity->shouldReceive('setContentLength')->once()->with(7);
        $this->_entity->shouldReceive('setContentType')->once()->with('fooey');

        $this->_sut->decode($this->_response);

        $this->assertTrue($context->response === $this->_response);
    }

    function testIsEncoded()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('something');
        $this->_response->shouldReceive('getHeaderValue')->once()->with($this->getHeaderName())->andReturn('anythihng');
        $this->assertTrue($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedNoHeader()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('something');
        $this->_response->shouldReceive('getHeaderValue')->once()->with($this->getHeaderName())->andReturn(null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedEmptyContent()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('');
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedNullContent()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn(null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    function testIsEncodedNullEntity()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn(null);
        $this->assertFalse($this->_sut->needsToBeDecoded($this->_response));
    }

    protected abstract function getHeaderName();

    protected abstract function getArrayOfCommands();

    protected abstract function buildSut();

    protected function getSut()
    {
        return $this->_sut;
    }
}

