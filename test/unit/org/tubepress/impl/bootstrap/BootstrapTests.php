<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'TubePressBootstrapperTest.php';

class BootstrapTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Bootstrap Tests");
		$suite->addTestSuite('org_tubepress_impl_bootstrap_TubePressBootstrapperTest');
		return $suite;
	}
}