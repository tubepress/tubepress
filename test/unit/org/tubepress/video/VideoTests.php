<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'VideoTest.php';
require_once 'factory/YouTubeVideoFactoryTest.php';

class VideoTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Utility Tests");
		$suite->addTestSuite('org_tubepress_video_VideoTest');
		$suite->addTestSuite('org_tubepress_video_factory_YouTubeVideoFactoryTest');
		return $suite;
	}
}
?>
