<?php

require_once 'api/ApiTestSuite.php';
require_once 'impl/ImplTestSuite.php';
require_once 'template/TemplateTestSuite.php';

class org_tubepress_FullTestSuite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();

		$suite->addTest(org_tubepress_api_ApiTestSuite::suite());
        $suite->addTest(org_tubepress_impl_ImplTestSuite::suite());
        $suite->addTest(org_tubepress_template_TemplateTestSuite::suite());

		return $suite;
	}
}
