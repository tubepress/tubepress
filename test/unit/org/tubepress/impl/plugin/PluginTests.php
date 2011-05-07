<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'galleryhtml/GalleryJsTest.php';
require_once 'gallerytemplate/EmbeddedPlayerNameTest.php';
require_once 'gallerytemplate/PaginationTest.php';
require_once 'gallerytemplate/PlayerTest.php';
require_once 'gallerytemplate/VideoMetaTest.php';
require_once 'providerresult/VideoPrependerTest.php';
require_once 'PluginManagerImplTest.php';

class PluginTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Filter Tests');
		$suite->addTestSuite('org_tubepress_impl_plugin_galleryhtml_GalleryJsTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_gallerytemplate_EmbeddedPlayerNameTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_gallerytemplate_PaginationTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_gallerytemplate_PlayerTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_gallerytemplate_VideoMetaTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_providerresult_VideoPrependerTest');
		$suite->addTestSuite('org_tubepress_impl_plugin_PluginManagerImplTest');
		return $suite;
	}
}

