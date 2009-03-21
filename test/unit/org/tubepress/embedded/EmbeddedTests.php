<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'JwFlvEmbeddedPlayerServiceTest.php';
require_once 'YouTubeEmbeddedPlayerServiceTest.php';

class EmbeddedTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Embedded Player Tests");
		$suite->addTestSuite('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerServiceTest');
		$suite->addTestSuite('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerServiceTest');
		return $suite;
	}
}
?>