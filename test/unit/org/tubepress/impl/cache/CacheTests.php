<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
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
?>
