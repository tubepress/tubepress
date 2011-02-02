<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'StategyManagerImplTest.php';
require_once 'FilterManagerImplTest.php';

class PatternsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Patterns Test');
		$suite->addTestSuite('org_tubepress_impl_patterns_StrategyManagerImplTest');
		$suite->addTestSuite('org_tubepress_impl_patterns_FilterManagerImplTest');
		return $suite;
	}
}
?>
