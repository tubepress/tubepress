<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'wordpress/AdminTest.php';
require_once 'wordpress/WidgetTest.php';
require_once 'wordpress/MainTest.php';

class EnvTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Env Tests');
		$suite->addTestSuite('org_tubepress_impl_env_wordpress_AdminTest');
		$suite->addTestSuite('org_tubepress_impl_env_wordpress_WidgetTest');
		$suite->addTestSuite('org_tubepress_impl_env_wordpress_MainTest');
		return $suite;
	}
}
