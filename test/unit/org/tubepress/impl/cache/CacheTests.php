<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'PearCacheLiteServiceTest.php';

class CacheTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Cache Tests");
		$suite->addTestSuite('org_tubepress_impl_cache_PearCacheLiteCacheServiceTest');
		return $suite;
	}
}
