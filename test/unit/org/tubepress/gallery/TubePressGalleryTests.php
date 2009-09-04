<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'TubePressGalleryImplTest.php';

class TubePressGalleryTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Gallery Tests");
		$suite->addTestSuite('org_tubepress_gallery_TubePressGalleryImplTest');
		return $suite;
	}
}
?>