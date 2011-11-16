<?php
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/http/transports/AbstractHttpTransport.class.php';

class FakeHttpTransport extends org_tubepress_impl_http_transports_AbstractHttpTransport
{
    public $headers = array('Content-Type' => 'text/html', 'Content-Encoding' => 'gzip');
    public $statusCode = 200;
    public $responseBody = 'response body';
    public $isAvailable = true;
    public $canHandle = true;
    public $headersString = 'headers string';

    protected function handleRequest(org_tubepress_api_http_HttpRequest $request)
    {
        return $this->responseBody;
    }

    protected function getTransportName()
    {
        return 'Fake';
    }

    protected function getResponseCode()
    {
        return $this->statusCode;
    }

    protected function getResponseMessage()
    {
        return $this->statusMessage;
    }

    public function isAvailable()
    {
        return $this->isAvailable;
    }

    public function canHandle(org_tubepress_api_http_HttpRequest $request)
    {
        return $this->canHandle;
    }
}

class org_tubepress_impl_http_transports_FakeTransportTest extends TubePressUnitTest
{
    private $_context;
    private $_sut;
    private $_request;
    private $_hmp;

    function setUp()
    {
        parent::setUp();

        $this->_sut = new FakeHttpTransport();
        $this->_context = new stdClass;
        $this->_request = \Mockery::mock(org_tubepress_api_http_HttpRequest::_);
        $this->_request->shouldReceive('__toString')->andReturn('request as string');
        $this->_context->request = $this->_request;

        ob_start();
    }

    function tearDown()
    {
        ob_end_clean();
    }

    function testRegularGet()
    {
        org_tubepress_impl_log_Log::setEnabled(true, array('tubepress_debug' => 'true'));

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_hmp = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);
        $this->_hmp->shouldReceive('getHeadersStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn($this->_sut->headersString);
        $this->_hmp->shouldReceive('getBodyStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn('body string');
        $this->_hmp->shouldReceive('getArrayOfHeadersFromRawHeaderString')->with($this->_sut->headersString)->andReturn($this->_sut->headers);

        $result = $this->_sut->execute($this->_context);

        $this->assertTrue($result);
        $response = $this->_context->response;
        $this->assertTrue($response instanceof org_tubepress_api_http_HttpResponse, 'Result is not a response');
        $this->assertTrue($response->getStatusCode() === 200);
        $this->assertTrue($response->getAllHeaders() === array('Content-Type' => 'text/html', 'Content-Encoding' => 'gzip'));

        $entity = $response->getEntity();
        $this->assertTrue($entity instanceof org_tubepress_api_http_HttpEntity);
        $this->assertTrue($entity->getContent() === 'body string', 'wrong response body');
        $this->assertTrue($entity->getContentLength() === 11, 'wrong content length');
        $this->assertTrue($entity->getContentType() === 'text/html', 'wrong content type');

        org_tubepress_impl_log_Log::setEnabled(false, array());
    }

    /**
     * @expectedException Exception
     */
    function testBadHeaderString()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_hmp = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);
        $this->_hmp->shouldReceive('getHeadersStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn($this->_sut->headersString);
        $this->_hmp->shouldReceive('getBodyStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn('body string');
        $this->_hmp->shouldReceive('getArrayOfHeadersFromRawHeaderString')->with($this->_sut->headersString)->andReturn(array());

        $this->_sut->execute($this->_context);
    }

    /**
     * @expectedException Exception
     */
    function testNoHeaderString()
    {
        $this->_sut->headersString = null;

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_hmp = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);
        $this->_hmp->shouldReceive('getHeadersStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn($this->_sut->headersString);
        $this->_hmp->shouldReceive('getBodyStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn('body string');
        $this->_hmp->shouldReceive('getArrayOfHeadersFromRawHeaderString')->with($this->_sut->headersString)->andReturn($this->_sut->headers);

        $this->_sut->execute($this->_context);
    }

    function testCannotHandle()
    {
        $this->_sut->canHandle = false;

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_hmp = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);
        $this->_hmp->shouldReceive('getHeadersStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn($this->_sut->headersString);
        $this->_hmp->shouldReceive('getBodyStringFromRawHttpMessage')->with($this->_sut->responseBody)->andReturn('body string');
        $this->_hmp->shouldReceive('getArrayOfHeadersFromRawHeaderString')->with($this->_sut->headersString)->andReturn($this->_sut->headers);

        $result = $this->_sut->execute($this->_context);

        $this->assertFalse($result);
    }

}