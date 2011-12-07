<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'PearCacheLiteServiceTest.php';

class org_tubepress_impl_cache_CacheTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_cache_PearCacheLiteCacheServiceTest'
		));
	}
}
