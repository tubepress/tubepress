<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_platform_scripts_ClassLoadingtest extends tubepress_api_test_TubePressUnitTest
{
    public function onSetup()
    {
        @unlink(TUBEPRESS_ROOT . "/src/php/scripts/classloading/classes.php");
    }

    public function onTearDown()
    {
       $this->onSetup();
    }

    public function testClassMapValidity()
    {
        $actualCoreClasses     = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/src');
        $actualVendorClasses   = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . '/vendor');
        $actualClasses         = array_merge($actualCoreClasses, $actualVendorClasses);
        $actualClassMap        = require TUBEPRESS_ROOT . '/src/php/scripts/classloading/classmap.php';

        ksort($actualClasses);

        $expected = $this->_getExpectedClassMap($actualClasses);

        $this->assertEquals($expected, $actualClassMap, $this->_messageForClassMapValidity($expected, $actualClassMap));
    }

    private function _messageForClassMapValidity(array $expected, array $actual)
    {
        $toReturn = "src/php/scripts/classloading/classmap.php does not have the right contents\n\n";

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

    private function _getExpectedClassMap(array $original)
    {
        $toReturn           = array();
        $pathExcludePattern = $this->_getPathExcludeRegex();
        $stringUtils        = new tubepress_util_impl_StringUtils();

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

            '/vendor/doctrine/',
            '/vendor/phpunit/',
            '/vendor/ehough/mockery/',
            '/vendor/composer/',
            '/vendor/ehough/oauth/tests',
            '/vendor/phpspec',
            '/vendor/phpdocumentor',
            '/vendor/sebastian',

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
            '/src/php/scripts/boot.php',
            '/ehough/tickertape/debug/',
            '/ehough/iconic/loader/',
            '/ehough/iconic/dumper/YamlDumper.php',
            '/ehough/iconic/dumper/XmlDumper.php',
            '/ehough/iconic/dumper/GraphvizDumper.php',
            '/ehough/iconic/ExpressionLanguage.php',
            '/ehough/iconic/Scope',
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

            '/ehough/stash/driver/Apc.php',
            '/ehough/stash/driver/Redis.php',

            '/ContainerAwareTrait\.php$',
            '/PrimaryBootstrapper.php',

            'Twig/Test',

            'JonnyW',
            '/vendor/jakoch',
            '/vendor/hamcrest',
            '/vendor/mockery',

            '/vendor/symfony/config',
            '/vendor/symfony/css-selector',
            '/vendor/symfony/dependency-injection',
            '/vendor/symfony/dom-crawler',
            '/vendor/symfony/polyfill-mbstring',
            '/vendor/symfony/process',
            '/vendor/symfony/yaml',
        );

        return '~' . implode('|', $excludes) . '~';
    }
}