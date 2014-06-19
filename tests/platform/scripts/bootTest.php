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

    public function onTearDown()
    {
        $this->assertTrue(unlink(TUBEPRESS_ROOT . '/src/platform/scripts/classloading/commonly-used-classes.php'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUncachedBoot()
    {
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/platform/scripts/classloading/commonly-used-classes.php', '<?php ') !== false);

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
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/platform/scripts/classloading/commonly-used-classes.php', '<?php ') !== false);

        $result = require TUBEPRESS_ROOT . '/src/platform/scripts/boot.php';

        $this->_testBasics($result);
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
            '/Gelf',
            '/Flowdock',
            '/ehough/stash/driver/sub/Sqlite',
            '/WinCache',
            'XcacheClassLoader',
            'ZendMonitorHandler',
            'TestHandler',
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