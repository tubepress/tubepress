<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'StategyManagerImplTest.php';

class PatternsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Patterns Test');
		$suite->addTestSuite('org_tubepress_impl_patterns_StrategyManagerImplTest');
		return $suite;
	}
}
?>
