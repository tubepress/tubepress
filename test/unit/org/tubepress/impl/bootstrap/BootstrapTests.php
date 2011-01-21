<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'FreeWordpressPluginBootstrapperTest.php';

class BootstrapTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Bootstrap Tests");
		$suite->addTestSuite('org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapperTest');
		return $suite;
	}
}
?>
