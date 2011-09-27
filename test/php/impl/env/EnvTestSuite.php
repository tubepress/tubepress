<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'wordpress/OptionsPageTest.php';
require_once 'wordpress/WidgetTest.php';
require_once 'wordpress/MainTest.php';
require_once 'wordpress/FormHandlerTest.php';

class org_tubepress_impl_env_EnvTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
	    	'org_tubepress_impl_env_wordpress_OptionsPageTest',
	    	'org_tubepress_impl_env_wordpress_WidgetTest',
	    	'org_tubepress_impl_env_wordpress_MainTest',
	    	'org_tubepress_impl_env_wordpress_FormHandlerTest',
		));
	}
}
