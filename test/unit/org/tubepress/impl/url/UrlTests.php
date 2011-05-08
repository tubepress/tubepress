<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'strategies/YouTubeUrlBuilderStrategyTest.php';
require_once 'strategies/VimeoUrlBuilderStrategyTest.php';
require_once 'DelegatingUrlBuilderTest.php';

class UrlTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress URL Tests");
		$suite->addTestSuite('org_tubepress_impl_url_strategies_YouTubeUrlBuilderStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_url_strategies_VimeoUrlBuilderStrategyTest');
        $suite->addTestSuite('org_tubepress_impl_url_DelegatingUrlBuilderTest');
		return $suite;
	}
}

