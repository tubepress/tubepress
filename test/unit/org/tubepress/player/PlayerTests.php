<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'impl/YouTubePlayerTest.php';
require_once 'impl/ModalPlayerTest.php';
require_once 'impl/NormalPlayerTest.php';

class PlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Pagination Tests");
		$suite->addTestSuite('org_tubepress_player_impl_YouTubePlayerTest');
		$suite->addTestSuite('org_tubepress_player_impl_NormalPlayerTest');
		$suite->addTestSuite('org_tubepress_player_impl_ModalPlayerTest');
		return $suite;
	}
}
?>