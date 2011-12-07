<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'LogImplTest.php';

class org_tubepress_impl_log_LogTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_log_LogImplTest'
		));
	}
}

