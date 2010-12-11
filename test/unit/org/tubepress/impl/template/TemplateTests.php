<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'SimpleTemplateTest.php';

class TemplateTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress Template Tests");
        $suite->addTestSuite('org_tubepress_impl_template_SimpleTemplateTest');
        return $suite;
    }
}
?>
