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
 * @covers tubepress_wordpress_impl_ActivationHook
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_wordpress_impl_ActivationHookTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_ActivationHook
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilesystemInterface;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSettingsFileReader;

    /**
     * @var string
     */
    private $_userContentDirectory;

    public function onSetup()
    {
        $this->_mockFilesystemInterface = $this->mock('ehough_filesystem_FilesystemInterface');
        $this->_mockSettingsFileReader  = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_sut = new tubepress_wordpress_impl_ActivationHook(

            $this->_mockSettingsFileReader,
            $this->_mockFilesystemInterface
        );

        $this->_userContentDirectory = sys_get_temp_dir() . '/activationHookTest';

        if (is_dir($this->_userContentDirectory)) {

            $this->recursivelyDeleteDirectory($this->_userContentDirectory);
        }

        mkdir($this->_userContentDirectory);

        define('WP_CONTENT_DIR', $this->_userContentDirectory);
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_userContentDirectory);
    }

    public function testNeedToInstall()
    {
        $this->_mockFilesystemInterface->shouldReceive('mirror')->once()->with(

            TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton',
            $this->_userContentDirectory . '/tubepress-content'
        );

        $file = tempnam(sys_get_temp_dir(), 'activationHookTest');

        if ($file === false) {

            $this->fail('Failed to create temporary file.');
        }

        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn($file);

        $this->_sut->execute();

        $this->assertFalse(is_file($file));
    }
}