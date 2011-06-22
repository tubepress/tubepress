<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTest.php';
require_once 'SimpleTemplateTest.php';

class org_tubepress_impl_template_TemplateTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_template_SimpleTemplateTest'
        ));
    }
}

