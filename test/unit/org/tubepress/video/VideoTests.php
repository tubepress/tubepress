<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'VideoTest.php';
require_once 'feed/retrieval/RetrievalTests.php';

class VideoTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Utility Tests");
		$suite->addTestSuite('org_tubepress_api_video_VideoTest');
		$suite->addTestSuite('RetrievalTests');
		return $suite;
	}
}
?>
