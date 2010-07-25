<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'YouTubeVideoFactoryTest.php';

class FactoryTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Video factory tests");
        $suite->addTestSuite('org_tubepress_video_factory_YouTubeVideoFactoryTest');
        return $suite;
    }
}
?>