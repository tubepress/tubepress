<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'MobileEspAgentDetectorTest.php';
require_once 'FastHttpClientTest.php';

class HttpTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress HTTP Tests");
		$suite->addTestSuite('org_tubepress_impl_http_MobileEspAgentDetectorTest');
                $suite->addTestSuite('org_tubepress_impl_http_FastHttpClientTest');
		return $suite;
	}
}
?>
