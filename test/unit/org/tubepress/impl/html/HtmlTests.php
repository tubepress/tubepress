<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'DefaultHtmlHandlerTest.php';
require_once 'strategies/SingleVideoStrategyTest.php';
require_once 'strategies/SoloPlayerStrategyTest.php';
require_once 'strategies/ThumbGalleryStrategyTest.php';

class HtmlTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress HTML Tests");
        
        $suite->addTestSuite('org_tubepress_impl_html_DefaultHtmlHandlerTest');
        $suite->addTestSuite('org_tubepress_impl_html_strategies_SingleVideoStrategyTest');
        $suite->addTestSuite('org_tubepress_impl_html_strategies_SoloPlayerStrategyTest');
        $suite->addTestSuite('org_tubepress_impl_html_strategies_ThumbGalleryStrategyTest');

        return $suite;
    }
}
?>
