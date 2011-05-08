<?php
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/ExtHttpStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/FopenStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/FsockOpenStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/CurlStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/http/clientimpl/strategies/StreamsStrategy.class.php';

class org_tubepress_impl_http_StressTests extends TubePressUnitTest {

    private $_sut;
    private $_args;
    
    function setup()
    {
        $this->_args = array(
            'method'       => 'GET',
            'timeout'      => 5,
            'httpversion' => '1.0',
            'user-agent'   => 'TubePress; http://tubepress.org',
            'headers'      => array(),
            'cookies'      => array(),
            'body'         => null,
            'compress'     => false,
            'decompress'   => true,
            'sslverify'   => true
        );
    }

    function testExtHttp()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_strategies_ExtHttpStrategy');
    }
    
    function dtestStreams()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_strategies_StreamsStrategy');
    }
    
    function dtestCurl()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_strategies_CurlStrategy');
    }
    
    function dtestFsockOpen()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_strategies_FsockOpenStrategy');
    }
    
    function dtestFopen()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_strategies_FopenStrategy');
    }
    
    function _doTest($class)
    {
        $client = new $class();
        
        $this->assertTrue($client->canHandle("http://tubepress.org/http_tests/1.file", $this->_args));
        
        $then = time();
        echo "Starting at $then\n";
        
        for ($x = 1; $x <= 50; $x++) {

            echo "Iteration $x with $class\n";
            
            $client->start();
            $result = $client->execute("http://tubepress.org/http_tests/$x.file", $this->_args);
            
            unset($result);
            $client->stop();
        }
        $now = time();
        echo "Ending at $now\n";

        echo sprintf("%s finished in %f seconds", $class, $now - $then);
    }
}


