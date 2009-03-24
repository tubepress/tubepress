<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'SimpleThumbnailServiceTest.php';

class ThumbnailTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Thumbnail Tests");
		$suite->addTestSuite('org_tubepress_thumbnail_SimpleThumbnailServiceTest');
		return $suite;
	}
}
?>