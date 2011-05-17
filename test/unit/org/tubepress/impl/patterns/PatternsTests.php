<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'cor/ChainGangTest.php';

class PatternsTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Patterns Test');
		$suite->addTestSuite('org_tubepress_impl_patterns_cor_ChainGangTest');
		return $suite;
	}
}

