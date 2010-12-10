<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'MobileEspAgentDetectorTest.php';

class AgentDetectorTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress HTTP Agent Detector Tests");
		$suite->addTestSuite('org_tubepress_impl_http_MobileEspAgentDetectorTest');
		return $suite;
	}
}
?>
