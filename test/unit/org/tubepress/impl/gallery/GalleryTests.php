<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'SimpleGalleryTest.php';
require_once 'strategies/SingleVideoStrategyTest.php';
require_once 'strategies/SoloPlayerStrategyTest.php';
require_once 'strategies/ThumbGalleryStrategyTest.php';

class GalleryTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Gallery Tests');
		$suite->addTestSuite('org_tubepress_impl_gallery_SimpleGalleryTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_SingleVideoStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_SoloPlayerStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_ThumbGalleryStrategyTest');
		return $suite;
	}
}
?>
