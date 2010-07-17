<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'impl/JwFlvEmbeddedPlayerServiceTest.php';
require_once 'impl/YouTubeEmbeddedPlayerServiceTest.php';
require_once 'impl/VimeoEmbeddedPlayerServiceTest.php';

class EmbeddedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Embedded Player Tests");
		$suite->addTestSuite('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerServiceTest');
		$suite->addTestSuite('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerServiceTest');
		$suite->addTestSuite('org_tubepress_embedded_impl_VimeoEmbeddedPlayerServiceTest');
		return $suite;
	}
}
?>