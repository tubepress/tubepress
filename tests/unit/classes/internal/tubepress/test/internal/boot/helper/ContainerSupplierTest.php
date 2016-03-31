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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @covers tubepress_internal_boot_helper_ContainerSupplier<extended>
 */
class tubepress_test_internal_boot_helper_ContainerSupplierTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_helper_ContainerSupplier
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockBootSettings;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUncachedContainerSupplier;

    /**
     * @var string
     */
    private $_mockSystemCacheDirectory;

    public function onSetup()
    {
        $this->_mockLogger                    = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockBootSettings              = $this->mock('tubepress_internal_boot_BootSettings');
        $this->_mockUncachedContainerSupplier = $this->mock('tubepress_internal_boot_helper_uncached_UncachedContainerSupplier');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_internal_boot_helper_ContainerSupplier(

            $this->_mockLogger,
            $this->_mockBootSettings
        );

        $this->_sut->___setUncachedContainerSupplier($this->_mockUncachedContainerSupplier);

        $this->_mockSystemCacheDirectory = sys_get_temp_dir() . '/tubepress-container-supplier-test';

        if (is_dir($this->_mockSystemCacheDirectory)) {

            $this->recursivelyDeleteDirectory($this->_mockSystemCacheDirectory);
        }

        $created = mkdir($this->_mockSystemCacheDirectory, 0755, true);

        $this->assertTrue($created);
    }

    public function onTeardown()
    {
        $this->recursivelyDeleteDirectory($this->_mockSystemCacheDirectory);

        $this->assertFalse(is_dir($this->_mockSystemCacheDirectory));
    }

    public function testSystemCacheDisabled()
    {
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(false);

        $this->_runUncachedContainerTest();
    }

    public function testNoSavedContainerOnFilesystem()
    {
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($this->_mockSystemCacheDirectory);

        $this->_runUncachedContainerTest();
    }

    public function testSavedContainerDoesNotContainerContainerClass()
    {
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($this->_mockSystemCacheDirectory);

        $fakeClass = '<?php class foo{}';
        $target    = $this->_mockSystemCacheDirectory . DIRECTORY_SEPARATOR . 'TubePressServiceContainer.php';

        file_put_contents($target, $fakeClass);

        $this->_runUncachedContainerTest();
    }

    public function testContainerClassExists()
    {
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);

        eval('class TubePressServiceContainer extends Symfony\Component\DependencyInjection\Container {}');

        $this->_confirmContainerReturned();
    }

    public function testContainerFoundOnFilesystem()
    {
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($this->_mockSystemCacheDirectory);

        $content = '<?php class TubePressServiceContainer extends Symfony\Component\DependencyInjection\Container {}';
        $target  = $this->_mockSystemCacheDirectory . DIRECTORY_SEPARATOR . 'TubePressServiceContainer.php';

        file_put_contents($target, $content);

        $this->_confirmContainerReturned();
    }

    private function _runUncachedContainerTest()
    {
        $fakeSymfonyContainer = $this->mock('\Symfony\Component\DependencyInjection\ContainerInterface');

        $this->_mockUncachedContainerSupplier->shouldReceive('getNewSymfonyContainer')->once()->andReturn($fakeSymfonyContainer);

        $fakeSymfonyContainer->shouldReceive('set')->once()->with('tubepress_api_ioc_ContainerInterface',      \Mockery::type('tubepress_api_ioc_ContainerInterface'));
        $fakeSymfonyContainer->shouldReceive('set')->once()->with('symfony_service_container',                 $fakeSymfonyContainer);
        $fakeSymfonyContainer->shouldReceive('set')->once()->with('tubepress_internal_logger_BootLogger',      $this->_mockLogger);
        $fakeSymfonyContainer->shouldReceive('set')->once()->with(tubepress_api_boot_BootSettingsInterface::_, $this->_mockBootSettings);

        $this->_confirmContainerReturned();
    }

    private function _confirmContainerReturned()
    {
        $actual = $this->_sut->getServiceContainer();

        $this->assertInstanceOf('tubepress_api_ioc_ContainerInterface', $actual);
    }
}