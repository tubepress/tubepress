<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'SimpleGalleryTest.php';

class GalleryTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Gallery Tests");
		$suite->addTestSuite('org_tubepress_impl_gallery_SimpleGalleryTest');
		return $suite;
	}
}
?>
