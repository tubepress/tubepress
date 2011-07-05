<?php
require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTest.php';
require_once 'SimpleProviderTest.php';

class org_tubepress_impl_provider_ProviderTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_provider_SimpleProviderTest'
        ));
    }
}

