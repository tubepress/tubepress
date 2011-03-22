<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'UrlTest.php';

class UrlTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress URL Tests");
		$suite->addTestSuite('org_tubepress_api_url_UrlTest');
		return $suite;
	}
}
?>
