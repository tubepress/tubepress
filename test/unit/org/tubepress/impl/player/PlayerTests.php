<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'DefaultPlayerHtmlGeneratorTest.php';

class PlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Player Tests");
		$suite->addTestSuite('org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest');
		return $suite;
	}
}

