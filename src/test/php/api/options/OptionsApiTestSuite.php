<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'OptionDescriptorTest.php';

class org_tubepress_api_options_OptionsApiTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_api_options_OptionDescriptorTest'
		));
	}
}
