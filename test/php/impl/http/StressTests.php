<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/ExtHttpCommand.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/FopenCommand.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/FsockOpenCommand.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/CurlCommand.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/clientimpl/commands/StreamsCommand.class.php';

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
        $this->_doTest('org_tubepress_impl_http_clientimpl_commands_ExtHttpCommand');
    }

    function dtestStreams()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_commands_StreamsCommand');
    }

    function dtestCurl()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_commands_CurlCommand');
    }

    function dtestFsockOpen()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_commands_FsockOpenCommand');
    }

    function dtestFopen()
    {
        $this->_doTest('org_tubepress_impl_http_clientimpl_commands_FopenCommand');
    }

    function _doTest($class)
    {
        $client = new $class();

        $then = time();
        echo "Starting at $then\n";

        for ($x = 1; $x <= 50; $x++) {

            echo "Iteration $x with $class\n";

            $context = new org_tubepress_impl_http_HttpClientChainContext("http://tubepress.org/http_tests/$x.file", $this->_args);
            $result = $client->execute($context);

            unset($result);
        }
        $now = time();
        echo "Ending at $now\n";

        echo sprintf("%s finished in %f seconds", $class, $now - $then);
    }
}


