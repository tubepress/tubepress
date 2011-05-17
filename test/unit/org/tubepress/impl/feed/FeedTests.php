<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'FeedInspectorChainTest.php';
require_once 'CacheAwareFeedFetcherTest.php';
require_once 'commands/YouTubeFeedInspectionCommandTest.php';
require_once 'commands/VimeoFeedInspectionCommandTest.php';

class FeedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Tests");
		$suite->addTestSuite('org_tubepress_impl_feed_FeedInspectorChainTest');
		$suite->addTestSuite('org_tubepress_impl_feed_CacheAwareFeedFetcherTest');
		$suite->addTestSuite('org_tubepress_impl_feed_commands_YouTubeFeedInspectionCommandTest');
		$suite->addTestSuite('org_tubepress_impl_feed_commands_VimeoFeedInspectionCommandTest');
		return $suite;
	}
}
