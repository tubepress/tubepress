<?php
require_once BASE . '/test/includes/TubePressUnitTest.php';
require_once 'commands/YouTubeUrlBuilderCommandTest.php';
require_once 'commands/VimeoUrlBuilderCommandTest.php';
require_once 'UrlBuilderChainTest.php';

class org_tubepress_impl_url_UrlTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_url_commands_YouTubeUrlBuilderCommandTest',
        	'org_tubepress_impl_url_commands_VimeoUrlBuilderCommandTest',
        	'org_tubepress_impl_url_UrlBuilderChainTest'
       	));
    }
}

