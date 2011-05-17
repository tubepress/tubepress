<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'HttpClientChainTest.php';
require_once 'clientimpl/EncodingTest.php';
require_once 'clientimpl/commands/CurlCommandTest.php';
require_once 'clientimpl/commands/ExtHttpCommandTest.php';
require_once 'clientimpl/commands/FopenCommandTest.php';
require_once 'clientimpl/commands/FsockOpenCommandTest.php';
require_once 'clientimpl/commands/StreamsCommandTest.php';
require_once 'StressTests.php';

class HttpTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress HTTP Tests");
        $suite->addTestSuite('org_tubepress_impl_http_HttpClientChainTest');
        $suite->addTestSuite('org_tubepress_impl_http_clientimpl_EncodingTest');
        $suite->addTestSuite('org_tubepress_impl_http_clientimpl_commands_CurlCommandTest');
        //$suite->addTestSuite('org_tubepress_impl_http_clientimpl_commands_ExtHttpCommandTest');
        $suite->addTestSuite('org_tubepress_impl_http_clientimpl_commands_FopenCommandTest');
        $suite->addTestSuite('org_tubepress_impl_http_clientimpl_commands_FsockOpenCommandTest');
        $suite->addTestSuite('org_tubepress_impl_http_clientimpl_commands_StreamsCommandTest');
        //$suite->addTestSuite('org_tubepress_impl_http_StressTests');
        return $suite;
    }
}

