<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'FeedInspectorChainTest.php';
require_once 'CacheAwareFeedFetcherTest.php';
require_once 'inspection/YouTubeFeedInspectionCommandTest.php';
require_once 'inspection/VimeoFeedInspectionCommandTest.php';
require_once 'UrlBuilderChainTest.php';
require_once 'urlbuilding/YouTubeUrlBuilderCommandTest.php';
require_once 'urlbuilding/VimeoUrlBuilderCommandTest.php';

class org_tubepress_impl_feed_FeedTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_feed_FeedInspectorChainTest',
		    'org_tubepress_impl_feed_CacheAwareFeedFetcherTest',
		    'org_tubepress_impl_feed_inspection_YouTubeFeedInspectionCommandTest',
		    'org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommandTest',
		    'org_tubepress_impl_feed_UrlBuilderChainTest',
		    'org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommandTest',
		    'org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommandTest'
		));
	}
}
