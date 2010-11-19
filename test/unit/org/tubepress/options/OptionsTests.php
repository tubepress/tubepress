<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'category/AdvancedTest.php';
require_once 'category/DisplayTest.php';
require_once 'category/EmbeddedTest.php';
require_once 'category/GalleryTest.php';
require_once 'category/MetaTest.php';
require_once 'category/WidgetTest.php';
require_once 'category/FeedTest.php';
require_once 'manager/SimpleOptionsManagerTest.php';
require_once 'reference/OptionsReferenceTest.php';
require_once 'storage/WordPressStorageManagerTest.php';
require_once 'validation/InputValidationServiceTest.php';
require_once 'form/FormHandlerTest.php';

class OptionsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress OptionsTests Tests");
		$suite->addTestSuite('org_tubepress_api_const_options_AdvancedTest');
		$suite->addTestSuite('org_tubepress_api_const_options_DisplayTest');
		$suite->addTestSuite('org_tubepress_api_const_options_EmbeddedTest');
		$suite->addTestSuite('org_tubepress_api_const_options_GalleryTest');
		$suite->addTestSuite('org_tubepress_api_const_options_MetaTest');
		$suite->addTestSuite('org_tubepress_api_const_options_WidgetTest');
		$suite->addTestSuite('org_tubepress_api_const_options_FeedTest');
		$suite->addTestSuite('org_tubepress_options_manager_SimpleOptionsManagerTest');
		$suite->addTestSuite('org_tubepress_util_OptionsReferenceTest');
		$suite->addTestSuite('org_tubepress_options_storage_WordPressStorageManagerTest');
		$suite->addTestSuite('org_tubepress_api_options_OptionValidatorTest');
		$suite->addTestSuite('org_tubepress_options_form_FormHandlerTest');
		return $suite;
	}
}
?>
