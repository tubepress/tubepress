<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'SingleVideoTest.php';

class SingleVideoTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Single Video Tests');
		$suite->addTestSuite('org_tubepress_single_VideoTest');
		return $suite;
	}
}

