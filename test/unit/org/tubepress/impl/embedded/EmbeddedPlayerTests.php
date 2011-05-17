<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'commands/JwFlvCommandTest.php';
require_once 'commands/VimeoCommandTest.php';
require_once 'commands/YouTubeIframeCommandTest.php';
require_once 'EmbeddedPlayerUtilsTest.php';
require_once 'EmbeddedPlayerChainTest.php';

class EmbeddedPlayerTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('TubePress Embedded Player Tests');
		$suite->addTestSuite('org_tubepress_impl_embedded_commands_JwFlvCommandTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_commands_VimeoCommandTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_commands_YouTubeIframeCommandTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest');
		$suite->addTestSuite('org_tubepress_impl_embedded_EmbeddedPlayerChainTest');
		return $suite;
	}
}
