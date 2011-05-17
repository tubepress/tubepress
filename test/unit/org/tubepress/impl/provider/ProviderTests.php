<?php
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once 'SimpleProviderTest.php';

class ProviderTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TubePress Video Provider Tests');
        $suite->addTestSuite('org_tubepress_impl_provider_SimpleProviderTest');
        return $suite;
    }
}

