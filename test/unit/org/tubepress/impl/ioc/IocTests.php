<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'FreeWordPressPluginIocServiceTest.php';
require_once 'IocContainerTest.php';

class IocTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress IOC Tests');
		$suite->addTestSuite('org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest');
		$suite->addTestSuite('org_tubepress_impl_ioc_IocContainerTest');
		return $suite;
	}
}

