<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'SimpleCacheServiceTest.php';

class CacheTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Cache Tests");
		$suite->addTestSuite('org_tubepress_cache_PearCacheLiteCacheServiceTest');
		return $suite;
	}
}
?>