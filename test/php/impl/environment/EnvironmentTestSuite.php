<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'SimpleEnvironmentDetectorTest.php';

class org_tubepress_impl_environment_EnvironmentTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
	    	'org_tubepress_impl_environment_SimpleEnvironmentDetectorTest'
		));
	}
}
