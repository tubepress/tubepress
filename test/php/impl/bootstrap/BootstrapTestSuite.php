<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'TubePressBootstrapperTest.php';

class org_tubepress_impl_bootstrap_BootstrapTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_bootstrap_TubePressBootstrapperTest'
		));
	}
}