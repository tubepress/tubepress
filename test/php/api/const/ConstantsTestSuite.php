<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'ConstantsTest.php';

class org_tubepress_api_const_ConstantsTestSuite
{
	public static function suite()
	{
	    return new TubePressUnitTestSuite(array(
			'org_tubepress_api_const_ConstantsTest'
	    ));
	}
}
