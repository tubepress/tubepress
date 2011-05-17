<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'filters/galleryhtml/GalleryJsTest.php';
require_once 'filters/gallerytemplate/EmbeddedPlayerNameTest.php';
require_once 'filters/gallerytemplate/PaginationTest.php';
require_once 'filters/gallerytemplate/PlayerTest.php';
require_once 'filters/gallerytemplate/VideoMetaTest.php';
require_once 'filters/providerresult/VideoPrependerTest.php';
require_once 'PluginManagerImplTest.php';

class PluginTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Filter Tests');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_galleryhtml_GalleryJsTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerNameTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_gallerytemplate_PaginationTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_gallerytemplate_PlayerTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_gallerytemplate_VideoMetaTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_filters_providerresult_VideoPrependerTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_PluginManagerImplTest');
		return $suite;
	}
}

