<?php

require_once BASE . '/sys/classes/org/tubepress/spi/http/HttpMessageParser.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/http/DefaultHttpMessageParser.class.php';

abstract class org_tubepress_impl_http_transports_AbstractHttpTransportTest extends TubePressUnitTest {

    private $_sut;
    private $_args;
    private $_server;

    function setUp()
    {
        parent::setUp();
        $this->_sut = $this->_getSutInstance();
        org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));
        $this->_server = 'http://tubepress.org/http_tests';

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $mp  = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);

    	$mp->shouldReceive('getHeadersStringFromRawHttpMessage')->andReturnUsing(function ($data) {

    		$x = new org_tubepress_impl_http_DefaultHttpMessageParser();
    		return $x->getHeadersStringFromRawHttpMessage($data);

    	});
    	$mp->shouldReceive('getBodyStringFromRawHttpMessage')->andReturnUsing(function ($data) {

    		$x = new org_tubepress_impl_http_DefaultHttpMessageParser();
    		return $x->getBodyStringFromRawHttpMessage($data);

    	});
    	$mp->shouldReceive('getArrayOfHeadersFromRawHeaderString')->andReturnUsing(function ($data) {

    		$x = new org_tubepress_impl_http_DefaultHttpMessageParser();
    		return $x->getArrayOfHeadersFromRawHeaderString($data);

    	});
    	$mp->shouldReceive('getHeaderArrayAsString')->andReturnUsing(function ($data) {

    	    $x = new org_tubepress_impl_http_DefaultHttpMessageParser();
    	    return $x->getHeaderArrayAsString($data);
    	});
    }

    function testGet200Plain()
    {
        $this->_getTest('code-200-plain.php', 34, 'text/html', 200, $this->_contents200Plain());
    }

    function testGet404()
    {
        try {

            $this->_getTest('code-404.php', 0, 'text/html', 404, null);

        } catch (Exception $e) {

            if (! $this->_sut instanceof org_tubepress_impl_http_transports_FopenTransport) {

                throw $e;
            }
        }
    }

    protected function _getTest($path, $length, $type, $status, $expected, $message = null, $encoding = null)
    {
    	$this->prepareForRequest();

        $context = new stdClass();
        $context->request  = new org_tubepress_api_http_HttpRequest(org_tubepress_api_http_HttpRequest::HTTP_METHOD_GET, $this->_server . "/$path");
        $context->request->setHeader(org_tubepress_api_http_HttpRequest::HTTP_HEADER_USER_AGENT, 'TubePress');

        $result = $this->_sut->execute($context);

        $this->assertTrue($result, "Command did not return true that it had handled request ($result)");

        $response = $context->response;

        $this->assertTrue($response instanceof org_tubepress_api_http_HttpResponse, 'Reponse is not of type HttpResponse');

        $actualContentType = $response->getHeaderValue(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_TYPE);
        $this->assertTrue($actualContentType === $type || $actualContentType === "$type; charset=utf-8", "Expected Content-Type $type but got $actualContentType");

        $encoded = $response->getHeaderValue(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_ENCODING);
        $this->assertEquals($encoding, $encoded, "Expected encoding $encoding but got $encoded");

        $this->assertEquals($status, $response->getStatusCode(), "Expected status code $status but got " . $response->getStatusCode());

        $entity = $response->getEntity();
        $this->assertTrue($entity instanceof org_tubepress_api_http_HttpEntity);

        if ($response->getHeaderValue(org_tubepress_api_http_HttpResponse::HTTP_HEADER_TRANSFER_ENCODING) === 'chunked') {

            $data = @http_chunked_decode($entity->getContent());

            if ($data === false) {

                $data = $entity->getContent();
            }

        } else {

            $data = $entity->getContent();
        }


        $this->assertEquals($expected, $data);
    }

    protected abstract function _getSutInstance();

    protected function prepareForRequest()
    {
    	//override point
    }

    private function _contents200Plain()
    {
        return <<<EOT
random stuff!

here's another line

EOT;
    }
}

