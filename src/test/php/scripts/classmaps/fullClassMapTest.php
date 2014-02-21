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
class fullClassMapTest extends tubepress_test_TubePressUnitTest
{
    private $_actualClassMap;

    private static $_classesWeDontCareAbout = array(

        'ContainerAwareTrait',
        'InnerNameIterator',
        'InnerSizeIterator',
        'InnerTypeIterator',
        'TestMultiplePcreFilterIterator',
        'ehough_epilog_formatter_ChromePHPFormatter',
        'ehough_epilog_formatter_ElasticaFormatter',
        'ehough_epilog_formatter_GelfMessageFormatter',
        'ehough_epilog_formatter_JsonFormatter',
        'ehough_epilog_formatter_LogstashFormatter',
        'ehough_epilog_formatter_ScalarFormatter',
        'ehough_epilog_formatter_WildfireFormatter',
        'ehough_epilog_handler_AbstractSyslogHandler',
        'ehough_epilog_handler_AmqpHandler',
        'ehough_epilog_handler_BufferHandler',
        'ehough_epilog_handler_ChromePHPHandler',
        'ehough_epilog_handler_CouchDBHandler',
        'ehough_epilog_handler_CubeHandler',
        'ehough_epilog_handler_DoctrineCouchDBHandler',
        'ehough_epilog_handler_DynamoDbHandler',
        'ehough_epilog_handler_ElasticSearchHandler',
        'ehough_epilog_handler_ErrorLogHandler',
        'ehough_epilog_handler_FingersCrossedHandler',
        'ehough_epilog_handler_FirePHPHandler',
        'ehough_epilog_handler_GelfHandler',
        'ehough_epilog_handler_GroupHandler',
        'ehough_epilog_handler_HipChatHandler',
        'ehough_epilog_handler_LogglyHandler',
        'ehough_epilog_handler_MailHandler',
        'ehough_epilog_handler_MissingExtensionException',
        'ehough_epilog_handler_MongoDBHandler',
        'ehough_epilog_handler_NativeMailerHandler',
        'ehough_epilog_handler_NewRelicHandler',
        'ehough_epilog_handler_PushoverHandler',
        'ehough_epilog_handler_RavenHandler',
        'ehough_epilog_handler_RedisHandler',
        'ehough_epilog_handler_RotatingFileHandler',
        'ehough_epilog_handler_SocketHandler',
        'ehough_epilog_handler_StreamHandler',
        'ehough_epilog_handler_SwiftMailerHandler',
        'ehough_epilog_handler_SyslogHandler',
        'ehough_epilog_handler_SyslogUdpHandler',
        'ehough_epilog_handler_TestHandler',
        'ehough_epilog_handler_ZendMonitorHandler',
        'ehough_epilog_handler_fingerscrossed_ActivationStrategyInterface',
        'ehough_epilog_handler_fingerscrossed_ChannelLevelActivationStrategy',
        'ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy',
        'ehough_epilog_handler_syslogudp_UdpSocket',
        'ehough_epilog_processor_IntrospectionProcessor',
        'ehough_epilog_processor_MemoryPeakUsageProcessor',
        'ehough_epilog_processor_MemoryProcessor',
        'ehough_epilog_processor_MemoryUsageProcessor',
        'ehough_epilog_processor_ProcessIdProcessor',
        'ehough_epilog_processor_PsrLogMessageProcessor',
        'ehough_epilog_processor_UidProcessor',
        'ehough_epilog_processor_WebProcessor',
        'ehough_filesystem_FilesystemTestCase',
        'ehough_finder_iterator_Iterator',
        'ehough_iconic_ExpressionLanguage',
        'ehough_iconic_SimpleXMLElement',
        'ehough_iconic_dumper_GraphvizDumper',
        'ehough_iconic_dumper_XmlDumper',
        'ehough_iconic_dumper_YamlDumper',
    );

    private static $_patternsToIgnore = array(

        '.+_test_.+',
        '.+_iconic_loader_.+',
        '.*Test$',
        '^ehough_finder_iterator_Mock.+',
        '^ehough_finder_fakeadapter_.+',
        '^ehough_iconic_Scope.*',
        '.+TestCase$',
        '^ehough_stash_driver_sub_Sqlite.*',
        '^ehough_tickertape_debug_.+',
        '^ehough_pulsar_Debug.*',
    );

    public function onSetup()
    {
        $this->_actualClassMap = require dirname(__FILE__) . '/../../../../main/php/scripts/classmaps/full.php';
    }

    public function testClassMapValidity()
    {
        $this->assertTrue(is_array($this->_actualClassMap));

        $this->assertTrue(tubepress_impl_util_LangUtils::isAssociativeArray($this->_actualClassMap));

        foreach ($this->_actualClassMap as $className => $path) {

            $this->assertTrue(is_readable($path) && is_file($path), "$path is not readable. Fix it!");
        }
    }

    public function testAllClassesInClassMap()
    {
        $pathsToSearch = array(

            '/src/main/php/classes',
            '/vendor/ehough/chaingang',
            '/vendor/ehough/coauthor',
            '/vendor/ehough/contemplate',
            '/vendor/ehough/curly',
            '/vendor/ehough/epilog',
            '/vendor/ehough/filesystem',
            '/vendor/ehough/finder',
            '/vendor/ehough/iconic',
            '/vendor/ehough/pulsar',
            '/vendor/ehough/shortstop',
            '/vendor/ehough/stash',
            '/vendor/ehough/tickertape',
        );

        $expectedClassMap = array();

        foreach ($pathsToSearch as $pathToSearch) {

            $map              = \Symfony\Component\ClassLoader\ClassMapGenerator::createMap(TUBEPRESS_ROOT . $pathToSearch);
            $expectedClassMap = array_merge($expectedClassMap, $map);
        }

        $missing = array();

        foreach ($expectedClassMap as $className => $path) {

            if (!array_key_exists($className, $this->_actualClassMap)) {

                $missing[] = $className;
            }
        }

        $missing = array_filter($missing, array($this, '__filterClassesWeDontCareAbout'));

        if (empty($missing)) {

            $this->assertTrue(true);
            return;
        }

        $missing = array_unique($missing);
        sort($missing);

        $message = "The following classes are missing from the full classmap: \n\n" . implode("\n", $missing);
        $this->fail($message);
    }

    public function __filterClassesWeDontCareAbout($className)
    {
        if (in_array($className, self::$_classesWeDontCareAbout)) {

            return false;
        }

        $patterns = implode('|', self::$_patternsToIgnore);

        if (preg_match("/(?:$patterns)/", $className, $matches) === 1) {

            return false;
        }

        return true;
    }
}