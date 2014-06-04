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
        define('TUBEPRESS_CONTENT_DIRECTORY', TUBEPRESS_ROOT . '/src/test/platform/fixtures/scripts/boot');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUncachedBoot()
    {
        $this->_removeCachedContainer();

        $result = require TUBEPRESS_ROOT . '/src/main/platform/scripts/boot.php';

        $this->_testBasics($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCachedBoot()
    {
        $then = $this->_getClassesAndInterfacesSnapshot();

        $result = require TUBEPRESS_ROOT . '/src/main/platform/scripts/boot.php';

        $now = $this->_getClassesAndInterfacesSnapshot();

        $this->_testClassMapValidity(array_diff($now, $then));

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

        $nextBoot = require TUBEPRESS_ROOT . '/src/main/platform/scripts/boot.php';

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

    private function _testClassMapValidity(array $expected)
    {
        $expected = array_filter($expected, array($this, '__classesToExcludeFromBootMap'));
        $classMap = require TUBEPRESS_ROOT . '/src/main/platform/scripts/classmaps/bootstrap.php';
        sort($expected);
        ksort($classMap);

        $this->assertTrue(is_array($classMap));

        $langUtils = new tubepress_impl_util_LangUtils();

        $this->assertTrue($langUtils->isAssociativeArray($classMap), 'Boot classmap is not an associative array');

        foreach ($classMap as $className => $path) {

            $this->assertTrue(is_readable($path) && is_file($path), "$path is not readable. Fix it!");
        }

        $this->assertEquals($expected, array_keys($classMap));
    }

    public function __classesToExcludeFromBootMap($className)
    {
        return !in_array($className, array(

            '__tubePressBoot',
            'TubePressServiceContainer',
            'tubepress_impl_boot_PrimaryBootstrapper'
        ));
    }
}