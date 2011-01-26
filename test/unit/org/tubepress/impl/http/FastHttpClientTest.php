<?php
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/http/FastHttpClient.class.php';

class org_tubepress_impl_http_FastHttpClientTest extends TubePressUnitTest {

    private $_sut;
    private $_args;
    
    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_http_FastHttpClient();
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
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with(array(
            'org_tubepress_impl_http_clientimpl_strategies_ExtHttpStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_CurlStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_StreamsStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_FopenStrategy',
            'org_tubepress_impl_http_clientimpl_strategies_FsockOpenStrategy'), new PHPUnit_Framework_Constraint_IsEqual('http://tubepress.org'),
                     new PHPUnit_Framework_Constraint_IsEqual($this->_args))
                  ->will($this->returnValue('foo'));
        }
        
        return $mock;
    }

    function testPostArray()
    {
        $this->_args['method'] = 'POST';
        $this->_args['body'] = 'one=two';
        $this->_args['headers']['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
        $this->_args['headers']['Content-Length'] = 7;
        $this->assertEquals('foo', $this->_sut->post('http://tubepress.org', array('one' => 'two')));
    }

    function testPostString()
    {
        $this->_args['method'] = 'POST';
        $this->_args['body'] = 'NADA';
        $this->_args['headers']['Content-Length'] = 4;
        $this->assertEquals('foo', $this->_sut->post('http://tubepress.org', 'NADA'));
    }

    function testGetGoodUrl()
    {
        unset($this->_args['headers']['Content-Length']);
        $this->assertEquals('foo', $this->_sut->get('http://tubepress.org'));
    }

    /**
     * @expectedException Exception
     */
    function testGetBadUrl()
    {
        $this->_sut->get();
    }
}
?>

