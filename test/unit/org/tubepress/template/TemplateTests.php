<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'SimpleTemplateTest.php';

class TemplateTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("TubePress Template Tests");
        $suite->addTestSuite('org_tubepress_template_SimpleTemplateTest');
        return $suite;
    }
}
?>