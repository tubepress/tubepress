<?php
require_once 'data.inc';

abstract class org_tubepress_impl_http_contentencoding_AbstractDecompressorTest extends TubePressUnitTest {

    private $_sut;

    private $_context;

    private $_response;

    function setUp()
    {
        parent::setUp();
        $this->_sut = $this->buildSut();


        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_context = new stdClass;
        $this->_context->response = $this->_response;
        ob_start();
    }

    function tearDown()
    {
        ob_end_clean();
    }

    /**
     * @expectedException Exception
     */
    function testCannotDecompressObject()
    {
        $entity   = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);

        $this->_response->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn($this->getHeaderValue());
        $entity->shouldReceive('getContent')->once()->andReturn($this->_sut);
        $this->_sut->execute($this->_context);
    }

    /**
     * @expectedException Exception
     */
    function testCannotDecompressReservedBitsSet()
    {
        global $data;

        $toSend = $this->getCompressed($data, 9);
        $entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);

        $toSend[3] = "\x11";

        $this->_response->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn($this->getHeaderValue());
        $entity->shouldReceive('getContent')->once()->andReturn($toSend);
        $this->_sut->execute($this->_context);
    }

    /**
     * @expectedException Exception
     */
    function testCannotDecompressString()
    {
        $entity   = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);

        $this->_response->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn($this->getHeaderValue());
        $entity->shouldReceive('getContent')->once()->andReturn('something that cannot be decompressed');
        $this->_sut->execute($this->_context);
    }

    function testNoContentEncodingHeader()
    {
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn(null);

        $this->assertFalse($this->_sut->execute($this->_context));
    }

    function testCompress()
    {
        org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => true));
        for ($x = 1; $x < 10; $x++) {

            $this->_testCompress($x);
        }
        org_tubepress_impl_log_Log::setEnabled(false, array());
    }

    function _testCompress($level)
    {
        global $data;

        $entity   = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);

        $this->_response->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING)->andReturn($this->getHeaderValue());
        $entity->shouldReceive('getContent')->once()->andReturn($this->getCompressed($data, $level));

        $result = $this->_sut->execute($this->_context);

        $this->assertTrue($result);

        $decoded = $this->_context->decoded;
        $this->assertNotNull($decoded);

        $this->assertEquals($data, $decoded);
    }

    protected abstract function buildSut();

    protected abstract function getHeaderValue();

    protected abstract function getCompressed($data, $level);

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
}

