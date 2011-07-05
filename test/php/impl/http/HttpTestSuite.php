<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'HttpClientChainTest.php';
require_once 'clientimpl/EncodingTest.php';
require_once 'clientimpl/commands/CurlCommandTest.php';
require_once 'clientimpl/commands/ExtHttpCommandTest.php';
require_once 'clientimpl/commands/FopenCommandTest.php';
require_once 'clientimpl/commands/FsockOpenCommandTest.php';
require_once 'clientimpl/commands/StreamsCommandTest.php';
require_once 'StressTests.php';

class org_tubepress_impl_http_HttpTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_http_HttpClientChainTest',
            'org_tubepress_impl_http_clientimpl_EncodingTest',
        	'org_tubepress_impl_http_clientimpl_commands_CurlCommandTest',
        	//'org_tubepress_impl_http_clientimpl_commands_ExtHttpCommandTest',
        	'org_tubepress_impl_http_clientimpl_commands_FopenCommandTest',
        	'org_tubepress_impl_http_clientimpl_commands_FsockOpenCommandTest',
        	'org_tubepress_impl_http_clientimpl_commands_StreamsCommandTest',
        	//'org_tubepress_impl_http_StressTests',
        ));
    }
}

