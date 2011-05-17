<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'commands/YouTubeUrlBuilderCommandTest.php';
require_once 'commands/VimeoUrlBuilderCommandTest.php';
require_once 'UrlBuilderChainTest.php';

class UrlTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress URL Tests");
        $suite->addTestSuite('org_tubepress_impl_url_commands_YouTubeUrlBuilderCommandTest');
        $suite->addTestSuite('org_tubepress_impl_url_commands_VimeoUrlBuilderCommandTest');
        $suite->addTestSuite('org_tubepress_impl_url_UrlBuilderChainTest');
        return $suite;
    }
}

