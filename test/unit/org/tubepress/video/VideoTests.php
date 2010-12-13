<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'VideoTest.php';

class VideoTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Video Tests");
		$suite->addTestSuite('org_tubepress_api_video_VideoTest');
		return $suite;
	}
}
?>
