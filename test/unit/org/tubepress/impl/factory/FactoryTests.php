<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'YouTubeVideoFactoryTest.php';

class FactoryTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Video factory tests");
        $suite->addTestSuite('org_tubepress_impl_factory_YouTubeVideoFactoryTest');
        return $suite;
    }
}
?>
