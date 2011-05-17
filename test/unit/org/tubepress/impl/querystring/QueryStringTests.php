<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'SimpleQueryStringServiceTest.php';

class QueryStringTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Query String Tests");
		$suite->addTestSuite('org_tubepress_impl_querystring_SimpleQueryStringServiceTest');
		return $suite;
	}
}

