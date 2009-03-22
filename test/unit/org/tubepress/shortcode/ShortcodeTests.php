<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'SimpleShortcodeServiceTest.php';

class ShortcodeTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("TubePress Shortcode Tests");
		$suite->addTestSuite('org_tubepress_shortcode_SimpleShortcodeServiceTest');
		return $suite;
	}
}
?>