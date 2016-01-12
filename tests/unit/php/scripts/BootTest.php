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
class tubepress_test_platform_scripts_BootTest extends tubepress_api_test_TubePressUnitTest
{
    public function onSetup()
    {
        define('TUBEPRESS_CONTENT_DIRECTORY', TUBEPRESS_ROOT . '/tests/unit/php/scripts/fixtures/mock-user-content-dir');
    }

    public function onTearDown()
    {
        $this->assertTrue(unlink(TUBEPRESS_ROOT . '/src/php/scripts/classloading/classes.php'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUncachedBoot()
    {
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/php/scripts/classloading/classes.php', '<?php ') !== false);

        $this->_removeCachedContainer();

        $result = require TUBEPRESS_ROOT . '/src/php/scripts/boot.php';

        $this->_testBasics($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCachedBoot()
    {
        $this->assertTrue(file_put_contents(TUBEPRESS_ROOT . '/src/php/scripts/classloading/classes.php', '<?php ') !== false);

        $result = require TUBEPRESS_ROOT . '/src/php/scripts/boot.php';

        $this->_testBasics($result);
    }

    private function _testBasics($result)
    {
        $this->assertInstanceOf('tubepress_api_ioc_ContainerInterface', $result);

        /**
         * @var $container tubepress_api_ioc_ContainerInterface
         */
        $container = $result;

        $this->assertTrue($container->has(tubepress_api_log_LoggerInterface::_), 'Container does not contain a logger');

        $logger = $container->get(tubepress_api_log_LoggerInterface::_);

        $this->assertInstanceOf('tubepress_api_log_LoggerInterface', $logger);

        $nextBoot = require TUBEPRESS_ROOT . '/src/php/scripts/boot.php';

        $this->assertSame($container, $nextBoot);

        $context = $container->get(tubepress_api_options_ContextInterface::_);
    }

    /**
     * @return bool
     */
    private function _removeCachedContainer()
    {
        if (file_exists(sys_get_temp_dir() . '/TubePress-99.99.99-ServiceContainer.php')) {

            $result = unlink(sys_get_temp_dir() . '/TubePress-99.99.99-ServiceContainer.php');

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
            '/src/php/scripts/boot.php',
            '/ContainerAwareTrait.php',
            '/vendor/composer/',
            '/ehough/tickertape/debug/',
            '/stash/driver/Sqlite.php',
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