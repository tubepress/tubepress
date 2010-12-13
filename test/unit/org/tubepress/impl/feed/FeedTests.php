<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';

class FeedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Feed Tests");
		return $suite;
	}
}
?>
