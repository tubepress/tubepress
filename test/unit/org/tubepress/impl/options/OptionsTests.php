<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'SimpleOptionsManagerTest.php';
require_once 'WordPressStorageManagerTest.php';
require_once 'SimpleOptionValidatorTest.php';
require_once 'FormHandlerTest.php';
require_once 'OptionsReferenceTest.php';

class OptionsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Options Tests");
		$suite->addTestSuite('org_tubepress_impl_options_SimpleOptionsManagerTest');
		$suite->addTestSuite('org_tubepress_impl_options_WordPressStorageManagerTest');
		$suite->addTestSuite('org_tubepress_impl_options_SimpleOptionValidatorTest');
		$suite->addTestSuite('org_tubepress_impl_options_FormHandlerTest');
		$suite->addTestSuite('org_tubepress_impl_options_OptionsReferenceTest');
		return $suite;
	}
}

