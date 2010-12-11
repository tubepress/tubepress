<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
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
?>
