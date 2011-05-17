<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
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

