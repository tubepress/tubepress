<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'GalleryTest.php';
require_once 'WidgetGalleryTest.php';

class GalleryTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Gallery Tests");
		$suite->addTestSuite('org_tubepress_gallery_GalleryTest');
		$suite->addTestSuite('org_tubepress_gallery_WidgetGalleryTest');
		return $suite;
	}
}
?>