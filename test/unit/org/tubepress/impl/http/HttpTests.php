<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'FastHttpClientTest.php';
require_once 'clientimpl/EncodingTest.php';
require_once 'clientimpl/strategies/FsockOpenStrategyTest.php';

class HttpTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress HTTP Tests");
                $suite->addTestSuite('org_tubepress_impl_http_FastHttpClientTest');
        	$suite->addTestSuite('org_tubepress_impl_http_clientimpl_EncodingTest');
		$suite->addTestSuite('org_tubepress_impl_http_clientimpl_strategies_FsockOpenStrategyTest');
		return $suite;
	}
}

