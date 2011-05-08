<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'LogImplTest.php';

class LogTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Log Tests');
		$suite->addTestSuite('org_tubepress_impl_log_LogImplTest');
		return $suite;
	}
}

