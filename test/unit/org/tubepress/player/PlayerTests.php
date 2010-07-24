<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'PlayerTest.php';

class PlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Player Tests");
		$suite->addTestSuite('org_tubepress_player_PlayerTest');
		return $suite;
	}
}
?>