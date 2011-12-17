<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'commands/JwFlvCommandTest.php';
require_once 'commands/VimeoCommandTest.php';
require_once 'commands/YouTubeIframeCommandTest.php';
require_once 'EmbeddedPlayerUtilsTest.php';
require_once 'EmbeddedPlayerChainTest.php';

class org_tubepress_impl_embedded_EmbeddedPlayerTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_embedded_commands_JwFlvCommandTest',
			'org_tubepress_impl_embedded_commands_VimeoCommandTest',
			'org_tubepress_impl_embedded_commands_YouTubeIframeCommandTest',
			'org_tubepress_impl_embedded_EmbeddedPlayerUtilsTest',
			'org_tubepress_impl_embedded_EmbeddedPlayerChainTest',
	    ));
	}
}
