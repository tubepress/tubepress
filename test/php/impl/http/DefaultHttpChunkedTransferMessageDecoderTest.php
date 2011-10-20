<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/DefaultHttpChunkedTransferMessageDecoder.class.php';

class org_tubepress_impl_http_DefaultHttpChunkedTransferMessageDecoderTest extends TubePressUnitTest {

    private $_sut;

    private $_response;

    private $_entity;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_DefaultHttpChunkedTransferMessageDecoder();
        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
    }

    function testDechunk()
    {
        $tests = $this->_decodeTestArray();
        foreach ($tests as $decoded => $encoded) {

            $result = $this->_sut->dechunk($encoded);
            $this->assertEquals($decoded, $result, "$result does not equal $decoded");
        }
    }

    function testContainsChunkedNotChunked()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('something');
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn('weird');
        $this->assertFalse($this->_sut->containsChunkedData($this->_response));
    }

    function testContainsChunked()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('something');
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn('chUnKeD');
        $this->assertTrue($this->_sut->containsChunkedData($this->_response));
    }

    function testContainsChunkedNoHeader()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('something');
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn(null);
        $this->assertFalse($this->_sut->containsChunkedData($this->_response));
    }

    function testContainsChunkedEmptyContent()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('');
        $this->assertFalse($this->_sut->containsChunkedData($this->_response));
    }

    function testContainsChunkedNullContent()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn(null);
        $this->assertFalse($this->_sut->containsChunkedData($this->_response));
    }

    function testContainsChunkedNullEntity()
    {
        $this->_response->shouldReceive('getEntity')->once()->andReturn(null);
        $this->assertFalse($this->_sut->containsChunkedData($this->_response));
    }

    //http://svn.php.net/viewvc/pecl/http/trunk/tests
    private function _decodeTestArray() {

        return array(
        <<<EOT
abra
cadabra
EOT
 => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0\r\nnothing\n",
        <<<EOT
abra
cadabra
EOT
        => "02\nab\n04\nra\nc\n06\nadabra\n0\nhidden\n",
      <<<EOT
abra
cadabra
all we got

EOT
       => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
       <<<EOT
this string is chunked encoded

EOT
       => "05\r\nthis \r\n07\r\nstring \r\n12\r\nis chunked encoded\r\n01\n\r\n00",
        <<<EOT
this string is chunked encoded

EOT
        => "005   \r\nthis \r\n     07\r\nstring \r\n12     \r\nis chunked encoded\r\n   000001     \n\r\n00"
       );
    }
}

