<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class bootTest extends tubepress_test_TubePressUnitTest
{
    public function onSetup()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', TUBEPRESS_ROOT . '/tests/platform/fixtures/scripts/boot');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::tearDownAfterClass();
    }

    public static function tearDownAfterClass()
    {
        if (file_exists(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections/minimal-boot.php')) {

            self::assertTrue(unlink(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections/minimal-boot.php'));
        }

        if (is_dir(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections')) {

            self::assertTrue(rmdir(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections'));
        }
    }

    public function testFullClassMapValidity()
    {
        $platFormClasses  = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/src/platform');
        $vendorClasses    = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/vendor');
        $expected         = array_merge($platFormClasses, $vendorClasses);
        $expected         = array_filter($expected, array($this, '__classesToExcludeFromBootMap'));
        $classMapFileFile = require TUBEPRESS_ROOT . '/src/platform/scripts/classmaps/full-vendor-and-platform.php';
        ksort($expected);
        ksort($classMapFileFile);

        $this->assertEquals($expected, $classMapFileFile, $this->_getExpectedClassMap($expected));
    }

    private function _getExpectedClassMap(array $expected)
    {
        $toReturn = "array(\n";

        foreach ($expected as $className => $path) {

            $toReturn .= "\t'$className' => " . str_replace(TUBEPRESS_ROOT, 'TUBEPRESS_ROOT . \'', $path) . "',\n";
        }

        $toReturn .= ');';

        return $toReturn;
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUncachedBoot()
    {
        $this->assertTrue(mkdir(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections'));
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections/minimal-boot.php', '<?php ') !== false);

        $this->_removeCachedContainer();

        $result = require TUBEPRESS_ROOT . '/src/platform/scripts/boot.php';

        $this->_testBasics($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCachedBoot()
    {
        $this->assertTrue(mkdir(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections'));
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/platform/scripts/class-collections/minimal-boot.php', '<?php ') !== false);

        $then = $this->_getClassesAndInterfacesSnapshot();

        $result = require TUBEPRESS_ROOT . '/src/platform/scripts/boot.php';

        $now = $this->_getClassesAndInterfacesSnapshot();

        $this->_testBasics($result);
    }

    private function _getClassesAndInterfacesSnapshot()
    {
        $classes = get_declared_classes();
        $interfaces = get_declared_interfaces();

        return array_merge($classes, $interfaces);
    }

    private function _testBasics($result)
    {
        $this->assertInstanceOf('tubepress_api_ioc_ContainerInterface', $result);

        /**
         * @var $container tubepress_api_ioc_ContainerInterface
         */
        $container = $result;

        $this->assertTrue($container->has(tubepress_api_log_LoggerInterface::_));

        $logger = $container->get(tubepress_api_log_LoggerInterface::_);

        $this->assertInstanceOf('tubepress_api_log_LoggerInterface', $logger);

        $nextBoot = require TUBEPRESS_ROOT . '/src/platform/scripts/boot.php';

        $this->assertSame($container, $nextBoot);

        $context = $container->get(tubepress_core_options_api_ContextInterface::_);
    }

    /**
     * @return bool
     */
    private function _removeCachedContainer()
    {
        if (file_exists(sys_get_temp_dir() . '/tubepress-service-container.php')) {

            $result = unlink(sys_get_temp_dir() . '/tubepress-service-container.php');

            $this->assertTrue($result);
        }
    }

    public function __classesToExcludeFromBootMap($className)
    {
        $thingsToIgnore = array(
            '/phpunit',
            '/mockery',
            '/src/test/',
            '/Tests/',
            '\\',
            '/vendor/symfony/',
            '/src/platform/scripts/boot.php',
            '/ContainerAwareTrait.php',
            '/vendor/composer/',
            '/ehough/tickertape/debug/',
            '/stash/driver/Sqlite.php',
            '/ehough/pulsar/Psr4ClassLoader.php',
            '/ehough/pulsar/Debug',
            '/ehough/iconic/loader/',
            '/ehough/iconic/dumper/YamlDumper.php',
            '/ehough/iconic/dumper/XmlDumper.php',
            '/ehough/iconic/dumper/GraphvizDumper.php',
            '/ehough/iconic/SimpleXMLElement.php',
            '/fingerscrossed/',
            '/Logstash',
            '/Loggly',
            '/ehough/epilog/formatter/Json',
            '/ehough/epilog/formatter/Html',
            '/Gelf',
            '/Flowdock',
            '/ehough/epilog/formatter/Elastica',
            '/ehough/epilog/formatter/ChromePHP',
            '/ehough/stash/driver/sub/Sqlite',
            '/WinCache',
            'XcacheClassLoader',
            'ZendMonitorHandler',
            'TestHandler',
            '/ehough/epilog/handler/Syslog',
            '/ehough/epilog/handler/syslogudp/UdpSocket',
            'SwiftMailerHandler',
            'GitProcessor',
            'RollbarHandler',
            'RedisHandler',
            'PushoverHandler',
            'RotatingFileHandler',
            'RavenHandler',
            'GitProcessor',
            'NewRelicHandler',
            'MongoDBHandler',
            'NativeMailerHandler',
            'FirePHP',
            'ElasticSearch',
            'HipChat',
            'LogEntriesHandler',
            'FingersCrossedHandler',
            'DynamoDb',
            'CouchDB',
            'CubeHandler',
            'BrowserConsoleHandler',
            'Amqp',
            'Wildfire',
            'ChromePHP'
        );

        foreach ($thingsToIgnore as $needle) {

            if (strpos($className, $needle) !== false) {

                return false;
            }
        }

        return true;
    }
}