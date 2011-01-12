<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'strategies/JwFlvEmbeddedPlayerTest.php';
require_once 'strategies/YouTubeEmbeddedPlayerTest.php';
require_once 'strategies/VimeoEmbeddedPlayerTest.php';
require_once 'EmbeddedPlayerUtilsTest.php';

class EmbeddedPlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Embedded Player Tests');
		$suite->addTestSuite('org_tubepress_impl_embedded_YouTubeEmbeddedPlayerTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_JwFlvEmbeddedPlayerTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_VimeoEmbeddedPlayerTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest');
		return $suite;
	}
}
?>
