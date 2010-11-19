<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'ShortcodeParserTest.php';

class ShortcodeTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Shortcode Tests');
		$suite->addTestSuite('org_tubepress_api_shortcode_ShortcodeParserTest');
		return $suite;
	}
}
?>
