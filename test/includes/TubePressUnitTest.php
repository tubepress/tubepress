<?php
require_once 'testBootStrap.php';
require_once dirname(__FILE__) . '/../../sys/classes/org/tubepress/impl/util/LangUtils.class.php';

abstract class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    public static $_mockCache;

    public function setUp()
    {
        self::$_mockCache = array();

        $ioc = \Mockery::mock('org_tubepress_api_ioc_IocService');
        $ioc->shouldReceive('get')->zeroOrMoreTimes()->andReturnUsing( function($className) {

            if (!array_key_exists($className, TubePressUnitTest::$_mockCache)) {

                org_tubepress_impl_classloader_ClassLoader::loadClass($className);

                $mock = \Mockery::mock($className);

                TubePressUnitTest::$_mockCache[$className] = $mock;

                if (!is_a($mock, $className)) {
                    TubePressUnitTest::fail("Failed to built mock of $className");
                }
            }

            return TubePressUnitTest::$_mockCache[$className];
        });

        org_tubepress_impl_ioc_IocContainer::setInstance($ioc);
    }

    public static function assertArrayEquality($expected, $actual)
    {
        foreach ($expected as $expectedName) {
            PHPUnit_Framework_Assert::assertTrue(in_array($expectedName, $actual), "Missing expected array value: $expectedName");
        }

        foreach ($actual as $actualName) {
            PHPUnit_Framework_Assert::assertTrue(in_array($actualName, $expected), "Extra array value: $actualName");
        }
    }

    public static function assertClassHasConstants($className, array $expected)
    {
        $actual = org_tubepress_impl_util_LangUtils::getDefinedConstants($className);

        TubePressUnitTest::assertArrayEquality($expected, $actual);
    }

    public function publicGetMock($className, $methods) {
        return parent::getMock($className, $methods);
    }
}