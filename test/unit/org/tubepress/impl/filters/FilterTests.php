<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'html/AjaxPaginationTest.php';
require_once 'template/EmbeddedPlayerNameTest.php';
require_once 'template/PaginationTest.php';
require_once 'template/PlayerTest.php';
require_once 'html/ThemeCssTest.php';
require_once 'template/VideoMetaTest.php';
require_once 'videos/VideoPrependerTest.php';

class FilterTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Filter Tests');
		$suite->addTestSuite('org_tubepress_impl_filters_html_AjaxPaginationTest');
		$suite->addTestSuite('org_tubepress_impl_filters_template_EmbeddedPlayerNameTest');
		$suite->addTestSuite('org_tubepress_impl_filters_template_PaginationTest');
		$suite->addTestSuite('org_tubepress_impl_filters_template_PlayerTest');
		$suite->addTestSuite('org_tubepress_impl_filters_html_ThemeCssTest');
		$suite->addTestSuite('org_tubepress_impl_filters_template_VideoMetaTest');
		$suite->addTestSuite('org_tubepress_impl_filters_videos_VideoPrependerTest');
		return $suite;
	}
}
?>
