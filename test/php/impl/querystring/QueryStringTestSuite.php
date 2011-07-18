<?php
require_once BASE . '/test/includes/TubePressUnitTest.php';
require_once 'SimpleQueryStringServiceTest.php';

class org_tubepress_impl_querystring_QueryStringTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_querystring_SimpleQueryStringServiceTest'
		));
	}
}

