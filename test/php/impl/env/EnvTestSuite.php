<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'wordpress/AdminTest.php';
require_once 'wordpress/WidgetTest.php';
require_once 'wordpress/MainTest.php';

class org_tubepress_impl_env_EnvTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
	    	'org_tubepress_impl_env_wordpress_AdminTest',
	    	'org_tubepress_impl_env_wordpress_WidgetTest',
	    	'org_tubepress_impl_env_wordpress_MainTest'
		));
	}
}
