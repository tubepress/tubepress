<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'HttpRequestTest.php';

class org_tubepress_api_http_HttpApiTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_api_http_HttpRequestTest'
		));
	}
}
