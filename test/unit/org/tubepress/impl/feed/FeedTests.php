<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'DelegatingFeedInspectorTest.php';
require_once 'CacheAwareFeedFetcherTest.php';
require_once 'inspectionstrategies/YouTubeFeedInspectionStrategyTest.php';
require_once 'inspectionstrategies/VimeoFeedInspectionStrategyTest.php';

class FeedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Tests");
		$suite->addTestSuite('org_tubepress_impl_feed_DelegatingFeedInspectorTest');
		$suite->addTestSuite('org_tubepress_impl_feed_CacheAwareFeedFetcherTest');
		$suite->addTestSuite('org_tubepress_impl_feed_inspectionstrategies_YouTubeFeedInspectionStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_feed_inspectionstrategies_VimeoFeedInspectionStrategyTest');
		return $suite;
	}
}
