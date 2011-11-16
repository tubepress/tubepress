<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/HttpClientChain.class.php';

class org_tubepress_impl_http_HttpClientChainTest extends TubePressUnitTest {

    private $_sut;

    private $_request;
    private $_response;
    private $_url;
    private $_context;
    private $_actualCommands;
    private $_chain;

    function setup()
    {
        parent::setUp();

        $this->_sut = new org_tubepress_impl_http_HttpClientChain();

        $this->_request  = \Mockery::mock(org_tubepress_api_http_HttpRequest::_);
        $this->_response = \Mockery::mock(org_tubepress_api_http_HttpResponse::_);
        $this->_url      = \Mockery::mock(org_tubepress_api_url_Url::_);
        $this->_context  = new stdClass;
        $this->_actualCommands = array(

            'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand',
            'org_tubepress_impl_http_clientimpl_commands_CurlCommand',
			'org_tubepress_impl_http_clientimpl_commands_StreamsCommand',
            'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand',
        );
        $this->_chain = null;
    }

    function testExecuteAndHandle()
    {
        $this->_setupForNormalExecution();
        $this->_verifyHandledExecution();
    }

    function testGetWithBadEntity()
    {
        $this->_setupRequestWithBadEntity();
        $this->_setupDecoder();
        $this->_setupExecContext();
        $this->_setupChain();
        $this->_verifyNormalExecution();
    }

    function testGetWithEntity()
    {
        $this->_setupRequestWithEntity();
        $this->_setupDecoder();
        $this->_setupExecContext();
        $this->_setupChain();
        $this->_verifyNormalExecution();
    }

    /**
     * @expectedException Exception
     */
    function testGetNoCommandsCouldHandle()
    {
        $this->_setupForNormalExecution();
        $this->_chain->shouldReceive('execute')->once()->with($this->_context, $this->_actualCommands)->andReturn(false);

        $this->_request->shouldReceive('getMethod')->once()->andReturn('the method');
        $this->_request->shouldReceive('getUrl')->once()->andReturn('some url');

        $this->_sut->execute($this->_request);
    }

    function testGet()
    {
        $this->_setupForNormalExecution();
        $this->_verifyNormalExecution();
    }

    /**
    * @expectedException Exception
    */
    function testNoUrlInRequest()
    {
        $this->_request->shouldReceive('getMethod')->once()->andReturn('GET');
        $this->_request->shouldReceive('getUrl')->once()->andReturn(null);
        $this->_sut->execute($this->_request);
    }

    /**
     * @expectedException Exception
     */
    function testNoMethodInRequest()
    {
        $this->_request->shouldReceive('getMethod')->once()->andReturn(null);
        $this->_sut->execute($this->_request);
    }

    private function _verifyHandledExecution()
    {
        $this->_chain->shouldReceive('execute')->once()->with($this->_context, $this->_actualCommands)->andReturn(true);

        $handler = \Mockery::mock(org_tubepress_api_http_HttpResponseHandler::_);
        $handler->shouldReceive('handle')->once()->with($this->_response)->andReturn('final result');
        $this->_verifyDecoders();

        $result = $this->_sut->executeAndHandleResponse($this->_request, $handler);

        $this->assertEquals('final result', $result);
    }

    private function _verifyNormalExecution()
    {
        $this->_chain->shouldReceive('execute')->once()->with($this->_context, $this->_actualCommands)->andReturn(true);

        $this->_verifyDecoders();

        $result = $this->_sut->execute($this->_request);

        $this->assertTrue($this->_request === $this->_context->request);
        $this->assertTrue($this->_response === $result);
    }

    private function _verifyDecoders()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $transferDecoder = $ioc->get(org_tubepress_spi_http_HttpTransferDecoder::_);
        $contentDecoder  = $ioc->get(org_tubepress_spi_http_HttpContentDecoder::_);

        $transferDecoder->shouldReceive('needsToBeDecoded')->once()->with($this->_response)->andReturn(true);
        $contentDecoder->shouldReceive('needsToBeDecoded')->once()->with($this->_response)->andReturn(true);
        $transferDecoder->shouldReceive('decode')->once()->with($this->_response);
        $contentDecoder->shouldReceive('decode')->once()->with($this->_response);
    }

    private function _setupForNormalExecution()
    {
        $this->_setupRequestNoEntity();

        $this->_setupDecoder();

        $this->_setupExecContext();

        $this->_setupChain();
    }

    private function _setupChain()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_chain       = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $this->_context->response = $this->_response;
        $this->_chain->shouldReceive('createContextInstance')->once()->andReturn($this->_context);
    }

    private function _setupDecoder()
    {
        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $decomp = $ioc->get(org_tubepress_spi_http_HttpContentDecoder::_);
        $decomp->shouldReceive('getAcceptEncodingHeaderValue')->once()->andReturn('encoding header problem');
    }

    private function _setupExecContext()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        $commands = array(

            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP   => 'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL      => 'org_tubepress_impl_http_clientimpl_commands_CurlCommand',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS   => 'org_tubepress_impl_http_clientimpl_commands_StreamsCommand',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN     => 'org_tubepress_impl_http_clientimpl_commands_FopenCommand',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN => 'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand',
        );

        foreach ($commands as $name => $class) {

            $execContext->shouldReceive('get')->once()->with($name)->andReturn($name === org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
        }
    }

    private function _setupRequestBase()
    {
        $this->_request->shouldReceive('getMethod')->once()->andReturn('GET');
        $this->_request->shouldReceive('getUrl')->once()->andReturn($this->_url);

        $map = array(

            org_tubepress_api_http_HttpRequest::HTTP_HEADER_USER_AGENT => 'TubePress; http://tubepress.org',
            org_tubepress_api_http_HttpMessage::HTTP_HEADER_HTTP_VERSION => 'HTTP/1.0'
        );

        foreach ($map as $headerName => $headerValue) {

            $this->_request->shouldReceive('containsHeader')->once()->with($headerName)->andReturn(false);
            $this->_request->shouldReceive('setHeader')->once()->with($headerName, $headerValue);
        }

        $this->_request->shouldReceive('setHeader')->once()->with(org_tubepress_api_http_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING, 'encoding header problem');
    }

    private function _setupRequestNoEntity()
    {
        $this->_setupRequestBase();
        $this->_request->shouldReceive('getEntity')->once()->andReturn(null);
    }

    private function _setupRequestWithBadEntity()
    {
        $this->_setupRequestBase();
        $entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
        $entity->shouldReceive('getContentLength')->once()->andReturn(null);
        $entity->shouldReceive('getContentEncoding')->once()->andReturn(null);
        $entity->shouldReceive('getContent')->once()->andReturn(null);
        $entity->shouldReceive('getContentType')->once()->andReturn(null);
        $this->_request->shouldReceive('getEntity')->once()->andReturn($entity);
    }

    private function _setupRequestWithEntity()
    {
        $this->_setupRequestBase();
        $entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
        $entity->shouldReceive('getContentLength')->once()->andReturn(103);
        $entity->shouldReceive('getContentEncoding')->once()->andReturn('content encoding');
        $entity->shouldReceive('getContent')->once()->andReturn('content');
        $entity->shouldReceive('getContentType')->once()->andReturn('text/html');
        $this->_request->shouldReceive('getEntity')->once()->andReturn($entity);
        $this->_request->shouldReceive('setHeader')->once()->with(org_tubepress_api_http_HttpRequest::HTTP_HEADER_CONTENT_ENCODING, 'content encoding');
        $this->_request->shouldReceive('setHeader')->once()->with(org_tubepress_api_http_HttpRequest::HTTP_HEADER_CONTENT_TYPE, 'text/html');
        $this->_request->shouldReceive('setHeader')->once()->with(org_tubepress_api_http_HttpRequest::HTTP_HEADER_CONTENT_LENGTH, 103);
    }
}


