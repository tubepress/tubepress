<?php
require_once 'const/ConstantsTestSuite.php';
require_once 'options/OptionsApiTestSuite.php';
require_once 'provider/ProviderApiTestSuite.php';
require_once 'video/VideoApiTestSuite.php';
require_once 'http/HttpApiTestSuite.php';

class org_tubepress_api_ApiTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();

		$suite->addTest(org_tubepress_api_const_ConstantsTestSuite::suite());
		$suite->addTest(org_tubepress_api_options_OptionsApiTestSuite::suite());
		$suite->addTest(org_tubepress_api_provider_ProviderApiTestSuite::suite());
		$suite->addTest(org_tubepress_api_video_VideoApiTestSuite::suite());
		$suite->addTest(org_tubepress_api_http_HttpApiTestSuite::suite());

		return $suite;
	}
}
