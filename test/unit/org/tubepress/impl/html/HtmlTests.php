<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'DefaultHtmlHandlerTest.php';

class HtmlTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress HTML Tests");
        $suite->addTestSuite('org_tubepress_impl_html_DefaultHtmlHandlerTest');
        return $suite;
    }
}
?>
