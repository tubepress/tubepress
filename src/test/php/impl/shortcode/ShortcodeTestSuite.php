<?php
require_once dirname(__FILE__) . '/../../../../test/includes/TubePressUnitTest.php';
require_once 'SimpleShortcodeParserTest.php';
require_once 'ShortcodeHtmlGeneratorChainTest.php';
require_once 'commands/SearchInputCommandTest.php';
require_once 'commands/SearchOutputCommandTest.php';
require_once 'commands/SingleVideoCommandTest.php';
require_once 'commands/SoloPlayerCommandTest.php';
require_once 'commands/ThumbGalleryCommandTest.php';

class org_tubepress_impl_shortcode_ShortcodeTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_shortcode_SimpleShortcodeParserTest',
            'org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChainTest',
            'org_tubepress_impl_shortcode_commands_SearchInputCommandTest',
            'org_tubepress_impl_shortcode_commands_SearchOutputCommandTest',
            'org_tubepress_impl_shortcode_commands_SingleVideoCommandTest',
            'org_tubepress_impl_shortcode_commands_SoloPlayerCommandTest',
            'org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest',
        ));
	}
}