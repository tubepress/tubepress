<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';
require_once 'VideoFactoryChainTest.php';
require_once 'commands/YouTubeFactoryCommandTest.php';
require_once 'commands/VimeoFactoryCommandTest.php';

class org_tubepress_impl_factory_FactoryTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
			'org_tubepress_impl_factory_commands_YouTubeFactoryCommandTest',
        	'org_tubepress_impl_factory_VideoFactoryChainTest',
        	'org_tubepress_impl_factory_commands_VimeoFactoryCommandTest',
        ));
    }
}
