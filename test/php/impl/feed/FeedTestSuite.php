<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'FeedInspectorChainTest.php';
require_once 'CacheAwareFeedFetcherTest.php';
require_once 'commands/YouTubeFeedInspectionCommandTest.php';
require_once 'commands/VimeoFeedInspectionCommandTest.php';

class org_tubepress_impl_feed_FeedTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_feed_FeedInspectorChainTest',
		    'org_tubepress_impl_feed_CacheAwareFeedFetcherTest',
		    'org_tubepress_impl_feed_commands_YouTubeFeedInspectionCommandTest',
		    'org_tubepress_impl_feed_commands_VimeoFeedInspectionCommandTest',
		));
	}
}
