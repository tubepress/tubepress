<?php
require dirname(__FILE__) . '/../../../../PhpUnitLoader.php';
require_once 'ProviderTest.php';

class ProviderTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TubePress Video Provider Tests');
        $suite->addTestSuite('org_tubepress_impl_provider_ProviderTest');
        return $suite;
    }
}

