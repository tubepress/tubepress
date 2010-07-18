<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'impl/YouTubeUrlBuilderTest.php';
require_once 'impl/VimeoUrlBuilderTest.php';

class UrlTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress URL Tests");
		$suite->addTestSuite('org_tubepress_url_impl_YouTubeUrlBuilderTest');
		$suite->addTestSuite('org_tubepress_url_impl_VimeoUrlBuilderTest');
		return $suite;
	}
}
?>
