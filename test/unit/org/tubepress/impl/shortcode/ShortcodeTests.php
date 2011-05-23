<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'SimpleShortcodeParserTest.php';
require_once 'DefaultShortcodeHtmlGeneratorTest.php';
require_once 'commands/SearchInputCommandTest.php';
require_once 'commands/SingleVideoCommandTest.php';
require_once 'commands/SoloPlayerCommandTest.php';
require_once 'commands/ThumbGalleryCommandTest.php';

class ShortcodeTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Shortcode Tests');
		$suite->addTestSuite('org_tubepress_impl_shortcode_SimpleShortcodeParserTest');
		$suite->addTestSuite('org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChainTest');
		$suite->addTestSuite('org_tubepress_impl_shortcode_commands_SearchInputCommandTest');
		$suite->addTestSuite('org_tubepress_impl_shortcode_commands_SingleVideoCommandTest');
		$suite->addTestSuite('org_tubepress_impl_shortcode_commands_SoloPlayerCommandTest');
		$suite->addTestSuite('org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest');
		return $suite;
	}
}

