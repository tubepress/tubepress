<?php
require_once 'PHPUnit/Framework.php';
require_once 'DiggStylePaginationServiceTest.php';

class PaginationTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Pagination Tests");
		$suite->addTestSuite('org_tubepress_pagination_DiggStylePaginationServiceTest');
		return $suite;
	}
}
?>