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

/**
 * @covers tubepress_wordpress_impl_wp_ActivationHook
 * @runTestsInSeparateProcesses
 */
class tubepress_test_wordpress_impl_wp_ActivationHookTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_wp_ActivationHook
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockFilesystemInterface;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockSettingsFileReader;

    /**
     * @var string
     */
    private $_userContentDirectory;

    public function onSetup()
    {
        $this->_mockFilesystemInterface = $this->mock('Symfony\Component\Filesystem\Filesystem');
        $this->_mockSettingsFileReader  = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_sut = new tubepress_wordpress_impl_wp_ActivationHook(

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

    public function testTotallyFreshInstallYo()
    {
        $this->_mockFilesystemInterface->shouldReceive('mirror')->once()->with(

            TUBEPRESS_ROOT . '/src/add-ons/wordpress/resources/user-content-skeleton',
            $this->_userContentDirectory . '/tubepress-content'
        );


        $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/foo');

        $this->_sut->execute();

        $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/foo');

        $this->assertTrue(true);
    }
}