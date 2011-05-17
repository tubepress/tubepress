<?php
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/HttpClientChain.class.php';

class org_tubepress_impl_http_HttpClientChainTest extends TubePressUnitTest {

    private $_sut;
    private $_args;
    private $_result;
    
    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_http_HttpClientChain();
        $this->_args = array(
                       'method' => 'GET',
                       'timeout' => 5,
                       'httpversion' => '1.0',
'user-agent' => 'TubePress; http://tubepress.org',
                     'headers' => array('Accept-Encoding' => 'deflate;q=1.0, compress;q=0.5', 'Content-Length' => 0),
                     'cookies' => array(),
                     'body' => '',
                     'compress' => '',
                     'decompress' => true,
                     'sslverify' => true,
                     'ssl' => '',
                     'local' => ''
         );
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            $mock->expects($this->once())
                 ->method('execute')
                 ->with($this->anything(), array(
            'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand',
            'org_tubepress_impl_http_clientimpl_commands_CurlCommand',
            'org_tubepress_impl_http_clientimpl_commands_StreamsCommand',
            'org_tubepress_impl_http_clientimpl_commands_FopenCommand',
            'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand')
                   )
                  ->will($this->returnCallback(array($this, 'fake')));
        }
        
        return $mock;
    }

    function testPostString()
    {
        $this->_args['method'] = 'POST';
        $this->_args['body'] = 'NADA';
        $this->_args['headers']['Content-Length'] = 4;
        $this->_sut->post('http://tubepress.org/index.php', 'NADA');
        $this->assertEquals('foo', $this->_result);
    }

    function testGetGoodUrl()
    {
        unset($this->_args['headers']['Content-Length']);
        $this->_sut->get('http://tubepress.org');
        $this->assertEquals('foo', $this->_result);;
    }


    function fake() {
        $this->_result = 'foo';
        return true;
    }
}


