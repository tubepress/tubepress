<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/HttpResponseHandlerChain.class.php';

class org_tubepress_impl_http_HttpResponseHandlerChainTest extends TubePressUnitTest {

    private $_sut;

    private $_response;

    private $_entity;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_HttpResponseHandlerChain();
        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
    }

    function testNon200NobodyCouldHandle()
    {
        $this->_testNon200(false, 'An unknown HTTP error occurred. Please examine TubePress\'s debug output for further details');
    }

    function testNon200()
    {
        $this->_testNon200(true, 'this is an error message');
    }

    function test200NullEntity()
    {
        $this->_response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $this->_response->shouldReceive('getEntity')->once()->andReturn(null);

        $result = $this->_sut->handle($this->_response);

        $this->assertEquals('', $result);
    }

    function test200()
    {
        $this->_response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('money money money');

        $result = $this->_sut->handle($this->_response);

        $this->assertEquals('money money money', $result);
    }

    private function _testNon200($status, $message)
    {
        $this->_response->shouldReceive('getStatusCode')->once()->andReturn(401);

        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $chain    = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $context  = new stdClass;
        $context->messageToDisplay = 'this is an error message';
        $chain->shouldReceive('createContextInstance')->once()->andReturn($context);
        $chain->shouldReceive('execute')->once()->with($context, array(

                    'org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler'
        ))->andReturn($status);

        try {

            $this->_sut->handle($this->_response);

        } catch (Exception $e) {

            $this->assertEquals($message, $e->getMessage());
            return;
        }

        $this->fail('Did not throw exception');
    }
}

