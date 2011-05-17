<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'UrlTest.php';

class UrlApiTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress URL API Tests");
		$suite->addTestSuite('org_tubepress_api_url_UrlTest');
		return $suite;
	}
}
