<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'WordPressMessageServiceTest.php';

class MessageTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Message Tests');
		$suite->addTestSuite('org_tubepress_message_WordPressMessageServiceTest');
		return $suite;
	}
}
?>