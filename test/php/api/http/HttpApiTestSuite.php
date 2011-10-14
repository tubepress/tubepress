<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'HttpRequestTest.php';
require_once 'HttpEntityTest.php';
require_once 'UrlTest.php';

class org_tubepress_api_http_HttpApiTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_api_http_HttpRequestTest',
			'org_tubepress_api_http_UrlTest',
			'org_tubepress_api_http_HttpEntityTest',
		));
	}
}
