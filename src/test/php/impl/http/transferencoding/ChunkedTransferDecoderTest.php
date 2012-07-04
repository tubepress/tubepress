<?php
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/http/transferencoding/ChunkedTransferDecoder.class.php';

class org_tubepress_impl_http_transferencoding_ChunkedTransferDecoderTest extends TubePressUnitTest {

    private $_sut;

    private $_response;

    private $_entity;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_transferencoding_ChunkedTransferDecoder();
        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
    }

    /**
     * @expectedException Exception
     */
    function testDecodeBadData()
    {
        $tests = $this->_decodeTestArray();

        $context = new stdClass;
        $context->response = $this->_response;
        $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn('CHUNKED');
        $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
        $this->_entity->shouldReceive('getContent')->once()->andReturn('this is not encoded data\r\npoo');

        $result = $this->_sut->execute($context);
    }

    function testDecodeNotChunked()
    {
        $tests = $this->_decodeTestArray();
        foreach ($tests as $decoded => $encoded) {

            $context = new stdClass;
            $context->response = $this->_response;
            $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn('something else');

            $this->assertFalse($this->_sut->execute($context));
        }
    }

    function testDecode()
    {
        $tests = $this->_decodeTestArray();
        foreach ($tests as $decoded => $encoded) {

            $context = new stdClass;
            $context->response = $this->_response;
            $this->_response->shouldReceive('getHeaderValue')->once()->with(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING)->andReturn('chuNKeD');
            $this->_response->shouldReceive('getEntity')->once()->andReturn($this->_entity);
            $this->_entity->shouldReceive('getContent')->once()->andReturn($encoded);

            $result = $this->_sut->execute($context);

            $this->assertTrue($result);
            $this->assertEquals($decoded, $context->decoded, var_export($context->decoded, true) . " does not match expected " . var_export($decoded, true));
        }
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
        => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\n0\nhidden\n",
      <<<EOT
abra
cadabra
all we got

EOT
       => "02\r\nab\r\n04\r\nra\nc\r\n06\r\nadabra\r\n0c\r\n\nall we got\n",
       <<<EOT
this string is chunked encoded

EOT
       => "05\r\nthis \r\n07\r\nstring \r\n12\r\nis chunked encoded\r\n01\r\n\n\r\n00",
        <<<EOT
this string is chunked encoder

EOT
        => "005   \r\nthis \r\n     07\r\nstring \r\n12     \r\nis chunked encoder\r\n   000001     \r\n\n\r\n00"
       );
    }
}

