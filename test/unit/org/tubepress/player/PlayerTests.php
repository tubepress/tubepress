<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'PlayerTest.php';

class PlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Player Tests");
		$suite->addTestSuite('org_tubepress_api_player_PlayerTest');
		return $suite;
	}
}
?>
