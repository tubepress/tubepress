<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'YouTubeUrlBuilderTest.php';
require_once 'VimeoUrlBuilderTest.php';

class UrlTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress URL Tests");
		$suite->addTestSuite('org_tubepress_url_YouTubeUrlBuilderTest');
		$suite->addTestSuite('org_tubepress_url_VimeoUrlBuilderTest');
		return $suite;
	}
}
?>
