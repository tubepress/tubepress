<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'SimpleGalleryTest.php';
require_once 'strategies/SingleVideoStrategyTest.php';
require_once 'strategies/SoloPlayerStrategyTest.php';
require_once 'strategies/ThumbGalleryStrategyTest.php';
require_once 'filters/AjaxPaginationTest.php';
require_once 'filters/EmbeddedPlayerNameTest.php';
require_once 'filters/PaginationTest.php';
require_once 'filters/PlayerTest.php';
require_once 'filters/ThemeCssTest.php';
require_once 'filters/VideoMetaTest.php';

class GalleryTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Gallery Tests');
		$suite->addTestSuite('org_tubepress_impl_gallery_SimpleGalleryTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_SingleVideoStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_SoloPlayerStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_strategies_ThumbGalleryStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_AjaxPaginationTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_EmbeddedPlayerNameTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_PaginationTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_PlayerTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_ThemeCssTest');
		$suite->addTestSuite('org_tubepress_impl_gallery_filters_VideoMetaTest');
		return $suite;
	}
}
?>
