<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'strategies/JwFlvEmbeddedStrategyTest.php';
require_once 'strategies/YouTubeEmbeddedStrategyTest.php';
require_once 'strategies/VimeoEmbeddedStrategyTest.php';
require_once 'strategies/YouTubeIframeEmbeddedStrategyTest.php';
require_once 'EmbeddedPlayerUtilsTest.php';
require_once 'DelegatingEmbeddedPlayerTest.php';

class EmbeddedPlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Embedded Player Tests');
		$suite->addTestSuite('org_tubepress_impl_embedded_YouTubeEmbeddedStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_JwFlvEmbeddedStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_VimeoEmbeddedStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_YouTubeIframeEmbeddedStrategyTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_DelegatingEmbeddedPlayerTest');
		return $suite;
	}
}
?>
