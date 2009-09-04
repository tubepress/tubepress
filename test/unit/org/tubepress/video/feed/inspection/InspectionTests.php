<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'SimpleFeedInspectionServiceTest.php';

class InspectionTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress GData Inspection Tests");
		$suite->addTestSuite('org_tubepress_video_feed_inspection_SimpleFeedInspectionServiceTest');
		return $suite;
	}
}
?>