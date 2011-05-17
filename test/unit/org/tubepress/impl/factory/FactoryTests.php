<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'commands/YouTubeFactoryCommandTest.php';
require_once 'VideoFactoryChainTest.php';
require_once 'commands/VimeoFactoryCommandTest.php';

class FactoryTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Video factory tests");
        $suite->addTestSuite('org_tubepress_impl_factory_commands_YouTubeFactoryCommandTest');
        $suite->addTestSuite('org_tubepress_impl_factory_VideoFactoryChainTest');
        $suite->addTestSuite('org_tubepress_impl_factory_commands_VimeoFactoryCommandTest');
        return $suite;
    }
}
