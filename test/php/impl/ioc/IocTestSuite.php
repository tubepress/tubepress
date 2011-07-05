<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'FreeWordPressPluginIocServiceTest.php';
require_once 'IocContainerTest.php';

class org_tubepress_impl_ioc_IocTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest',
            'org_tubepress_impl_ioc_IocContainerTest'
		));
	}
}