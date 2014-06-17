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
class tubepress_test_platform_scripts_classloadingtest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var array
     */
    private static $_CONCATED_CLASSES;

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_CONCATED_CLASSES = array_merge(

            require '../fixtures/scripts/classloading/core.php',
            require '../fixtures/scripts/classloading/platform.php',
            require '../fixtures/scripts/classloading/vendor.php'
        );

        sort(self::$_CONCATED_CLASSES);
    }

    public function testClassMapValidity()
    {
        $actualCoreClasses     = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/src/core');
        $actualPlatformClasses = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/src/platform');
        $actualVendorClasses   = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/vendor');
        $actualClasses         = array_merge($actualCoreClasses, $actualPlatformClasses, $actualVendorClasses);
        $actualClassMap        = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';

        ksort($actualClasses);

        $classesNotInConcatenation = array();
        foreach ($actualClasses as $className => $path) {
            if (!in_array($className, self::$_CONCATED_CLASSES)) {
                $classesNotInConcatenation[$className] = $path;
            }
        }

        $expected = $this->_getExpectedClassMap($classesNotInConcatenation);

        $this->assertEquals($expected, $actualClassMap, var_export($expected, true));
    }

    public function testClassCollectionValidity()
    {
        if (!class_exists('tubepress_build_ClassCollectionBuilder', false)) {

            require TUBEPRESS_ROOT . '/build/bin/ClassCollectionBuilder.php';
        }

        $actual = tubepress_build_ClassCollectionBuilder::$CLASSES;

        $this->assertEquals(self::$_CONCATED_CLASSES, $actual, $this->_arrayDiff(self::$_CONCATED_CLASSES, $actual));
    }

    private function _getExpectedClassMap(array $original)
    {
        $toReturn           = array();
        $pathExcludePattern = $this->_getPathExcludeRegex();

        foreach ($original as $className => $path) {

            if (!$className) {

                continue;
            }

            if (preg_match_all($pathExcludePattern, $path, $matches) !== 0) {

                continue;
            }

            $toReturn[$className] = $path;
        }

        return $toReturn;
    }

    private function _getPathExcludeRegex()
    {
        $excludes = array(

            '/vendor/symfony/',
            '/vendor/phpunit/',
            '/vendor/ehough/mockery/',
            '/vendor/composer/',

            '/src/test/php/',
            '/Tests/',

            'Sqlite',
            'ChromePHP',
            '/fingerscrossed/',
            'WinCache',

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
            'SimpleXMLElement',
            '/src/platform/scripts/boot.php',
            '/ehough/tickertape/debug/',
            '/ehough/pulsar/Psr4ClassLoader.php',
            '/ehough/pulsar/Debug',
            '/ehough/iconic/loader/',
            '/ehough/iconic/dumper/YamlDumper.php',
            '/ehough/iconic/dumper/XmlDumper.php',
            '/ehough/iconic/dumper/GraphvizDumper.php',
            '/Logstash',
            '/Loggly',
            '/ehough/epilog/formatter/Json',
            '/ehough/epilog/formatter/Html',
            '/Gelf',
            '/Flowdock',
            '/ehough/epilog/formatter/Elastica',
            'XcacheClassLoader',
            'ZendMonitorHandler',
            'TestHandler',
            '/ehough/epilog/handler/Syslog',
            '/ehough/epilog/handler/syslogudp/UdpSocket',
            '/ApcClassLoader',
            '/ApcUniversal',
            '/ComposerClassLoader',
            '/ClassCollectionLoader',
            'BlackHole',
            'Xcache',
            'adapter/Fake',
            'adapter/Mock',
            'MailHandler',
            'ErrorLogHandler',
            'SocketHandler',
            'processor/Memory',
            'processor/ProcessId',
            'processor/Tag',
            'processor/Uid',
            'processor/Web',
            '/psr/test/',

            '/ContainerAwareTrait\.php$',
            '/PrimaryBootstrapper.php',
        );

        return '~' . implode('|', $excludes) . '~';
    }

    private function _arrayDiff(array $expected, array $actual)
    {
        $missing  = array_diff($expected, $actual);
        $extra    = array_diff($actual, $expected);
        $toReturn = '';

        if (!empty($missing)) {
            $toReturn = "The following elements are missing\n\n";
            foreach ($missing as $thing) {
                $toReturn .= "$thing\n";
            }
        }

        if (!empty($extra)) {
            $toReturn .= "\nThe following elements are extra:\n\n";
            foreach ($extra as $thing) {
                $toReturn .= "$thing\n";
            }
        }

        return $toReturn;
    }

    public function __classesToExcludeFromBootMap($className)
    {
        $thingsToIgnore = array(

            '\\',
        );

        foreach ($thingsToIgnore as $needle) {

            if (strpos($className, $needle) !== false) {

                return false;
            }
        }

        return true;
    }
}