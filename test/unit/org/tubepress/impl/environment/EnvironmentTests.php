<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'SimpleEnvironmentDetectorTest.php';

class EnvironmentTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Environment Detection Tests');
		$suite->addTestSuite('org_tubepress_impl_environment_SimpleEnvironmentDetectorTest');
		return $suite;
	}
}
