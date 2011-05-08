<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'ProviderResultTest.php';

class ProviderApiTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Provider API Tests");
		$suite->addTestSuite('org_tubepress_api_provider_ProviderResultTest');
		return $suite;
	}
}
