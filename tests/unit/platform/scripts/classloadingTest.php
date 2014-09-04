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
class tubepress_test_platform_scripts_ClassLoadingtest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var array
     */
    private static $_CONCATED_CLASSES;

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_CONCATED_CLASSES = array_merge(

            require TUBEPRESS_ROOT . '/build/config/classes-to-concat/boot.php'
        );

        sort(self::$_CONCATED_CLASSES);
    }

    public function onSetup()
    {
        @unlink(TUBEPRESS_ROOT . "/src/platform/scripts/classloading/classes.php");
    }

    public function onTearDown()
    {
       $this->onSetup();
    }

    public function testClassesExist()
    {
        foreach (self::$_CONCATED_CLASSES as $className) {
            if (!(class_exists($className) || interface_exists($className))) {

                $this->fail("$className does not exist");
            }
        }
        $this->assertTrue(true);
    }

    /**
     * @depends testClassesExist
     */
    public function testClassMapValidity()
    {
        $actualCoreClasses     = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/src');
        $actualVendorClasses   = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/vendor');
        $actualClasses         = array_merge($actualCoreClasses, $actualVendorClasses);
        $actualClassMap        = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';

        ksort($actualClasses);

        $expected = $this->_getExpectedClassMap($actualClasses);

        $this->assertEquals($expected, $actualClassMap, $this->_messageForClassMapValidity($expected, $actualClassMap));
    }

    private function _messageForClassMapValidity(array $expected, array $actual)
    {
        $toReturn = "src/platform/scripts/classloading/classmap.php does not have the right contents\n\n";

        $expectedClassNames = array_keys($expected);
        $actualClassNames   = array_keys($actual);
        $missingClasses     = array_diff($expectedClassNames, $actualClassNames);
        $extraClasses       = array_diff($actualClassNames, $expectedClassNames);

        if (!empty($missingClasses)) {

            $toReturn .= "classmap.php is missing the following classes:\n\n";

            foreach ($missingClasses as $className) {

                $toReturn .= "$className\n";
            }

            $toReturn .= "\n\n";
        }

        if (!empty($extraClasses)) {

            $toReturn .= "classmap.php contains the following classes that shouldn't be there:\n\n";

            foreach ($extraClasses as $className) {

                $toReturn .= "$className\n";
            }

            $toReturn .= "\n\n";
        }

        $toReturn .= "Here is the expected classmap, for your convenience:\n\n";

        $toReturn .= var_export($expected, true);

        $toReturn = str_replace(TUBEPRESS_ROOT, 'TUBEPRESS_ROOT . \'', $toReturn);
        $toReturn = str_replace('\'TUBEPRESS_ROOT', 'TUBEPRESS_ROOT', $toReturn);

        return $toReturn;
    }

    /**
     * @depends testClassMapValidity
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testConcatenationWorks()
    {
        $this->assertFileNotExists(TUBEPRESS_ROOT . "/src/platform/scripts/classloading/classes.php");

        if (!class_exists('tubepress_build_ClassCollectionBuilder', false)) {

            require TUBEPRESS_ROOT . '/build/bin/ClassCollectionBuilder.php';
        }

        $this->assertFileExists(TUBEPRESS_ROOT . "/src/platform/scripts/classloading/classes.php");
    }

    private function _getExpectedClassMap(array $original)
    {
        $toReturn           = array();
        $pathExcludePattern = $this->_getPathExcludeRegex();
        $stringUtils        = new tubepress_platform_impl_util_StringUtils();

        foreach ($original as $className => $path) {

            if (!$className) {

                continue;
            }

            if (preg_match_all($pathExcludePattern, $path, $matches) !== 0) {

                continue;
            }

            if ($stringUtils->endsWith($path, 'vendor/ehough/stash/src/main/php/ehough/stash/session/SessionHandlerInterface_Legacy.php')) {

                $path = str_replace('_Legacy.php', '.php', $path);
            }

            if ($stringUtils->endsWith($path, 'vendor/ehough/stash/src/main/php/ehough/stash/session/SessionHandlerInterface_Modern.php')) {

                $path = str_replace('_Modern.php', '.php', $path);
            }

            $toReturn[$className] = $path;
        }

        ksort($toReturn);
        return $toReturn;
    }

    private function _getPathExcludeRegex()
    {
        $excludes = array(

            '/vendor/symfony/',
            '/vendor/phpunit/',
            '/vendor/ehough/mockery/',
            '/vendor/composer/',
            '/vendor/ehough/oauth/tests',

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
            '/Gelf',
            '/Flowdock',
            'XcacheClassLoader',
            'ZendMonitorHandler',
            'TestHandler',
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

            'Twig_Test',
        );

        return '~' . implode('|', $excludes) . '~';
    }
}