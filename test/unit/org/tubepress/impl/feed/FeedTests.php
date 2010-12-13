<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'YouTubeUrlBuilderTest.php';
require_once 'VimeoUrlBuilderTest.php';

class FeedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Tests");
		$suite->addTestSuite('org_tubepress_impl_feed_YouTubeUrlBuilderTest');
		$suite->addTestSuite('org_tubepress_impl_feed_VimeoUrlBuilderTest');
		return $suite;
	}
}
?>
