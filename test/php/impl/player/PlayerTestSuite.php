<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTest.php';
require_once 'DefaultPlayerHtmlGeneratorTest.php';

class org_tubepress_impl_player_PlayerTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest'
        ));
	}
}

