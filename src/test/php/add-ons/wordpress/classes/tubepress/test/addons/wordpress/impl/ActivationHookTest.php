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

/**
 * @covers tubepress_addons_wordpress_impl_ActivationHook
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_addons_wordpress_impl_ActivationHookTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_ActivationHook
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilesystemInterface;

    /**
     * @var string
     */
    private $_userContentDirectory;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_ActivationHook();

        $this->_userContentDirectory = sys_get_temp_dir() . '/activationHookTest';

        if (is_dir($this->_userContentDirectory)) {

            $this->recursivelyDeleteDirectory($this->_userContentDirectory);
        }

        mkdir($this->_userContentDirectory);

        define('WP_CONTENT_DIR', $this->_userContentDirectory);

        $this->_mockFilesystemInterface = $this->createMockSingletonService('ehough_filesystem_FilesystemInterface');
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_userContentDirectory);
    }

    public function testNeedToInstall()
    {
        $this->_mockFilesystemInterface->shouldReceive('mirror')->once()->with(

            TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content',
            $this->_userContentDirectory . '/tubepress-content'
        );

        $this->_sut->execute();

        $this->assertTrue(true);
    }
}