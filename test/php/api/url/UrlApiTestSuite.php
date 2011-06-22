<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'UrlTest.php';

class org_tubepress_api_url_UrlApiTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_api_url_UrlTest'
		));
	}
}
