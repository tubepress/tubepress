<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'FeedResultTest.php';

class FeedApiTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress API Feed Tests");
		$suite->addTestSuite('org_tubepress_api_provider_ProviderResultTest');
		return $suite;
	}
}
?>
