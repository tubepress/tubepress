<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/HttpClientChain.class.php';

class org_tubepress_impl_http_HttpClientChainTest extends TubePressUnitTest {

    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_HttpClientChain();

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS)->andReturn(false);

        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->returnValue = 'foo';

        $chain = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
            'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand',
            'org_tubepress_impl_http_clientimpl_commands_CurlCommand',
            'org_tubepress_impl_http_clientimpl_commands_StreamsCommand',
            'org_tubepress_impl_http_clientimpl_commands_FopenCommand',
            'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand'
        ));
    }

    function testPostString()
    {
        $this->assertEquals('foo', $this->_sut->post('http://tubepress.org/index.php', 'NADA'));
    }

    function testGetGoodUrl()
    {
        $this->assertEquals('foo', $this->_sut->get('http://tubepress.org'));;
    }
}


