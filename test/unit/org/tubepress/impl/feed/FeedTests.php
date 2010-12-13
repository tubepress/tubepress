<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'YouTubeFeedInspectorTest.php';

class FeedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Tests");
		$suite->addTestSuite('org_tubepress_impl_feed_inspection_YouTubeFeedInspectorTest');
		return $suite;
	}
}
?>
