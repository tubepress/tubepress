<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTestSuite.php';

require_once 'DefaultHeadHtmlGeneratorTest.php';

class org_tubepress_impl_html_HtmlTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_html_DefaultHeadHtmlGeneratorTest'
        ));
    }
}
