<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_boot_DefaultIocContainerBootHelper<extended>
 */
class tubepress_test_impl_boot_DefaultIocContainerBootHelperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_DefaultIocContainerBootHelper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootConfigService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocContainer;

    private $_cacheDirectory;

    public function onSetup()
    {
        $this->_sut                   = new tubepress_impl_boot_DefaultIocContainerBootHelper();
        $this->_mockBootConfigService = $this->createMockSingletonService(tubepress_spi_boot_BootConfigService::_);
        $this->_cacheDirectory        = sys_get_temp_dir() . '/tubepress-test-' . mt_rand();
        $this->_mockIocContainer      = ehough_mockery_Mockery::mock('tubepress_impl_ioc_CoreIocContainer');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once TUBEPRESS_ROOT . '/src/test/resources/addons/FakeCompilerPass.php';
        require_once TUBEPRESS_ROOT . '/src/test/resources/addons/FakeExtension.php';
    }

    public function onTearDown()
    {
        $this->deleteDirectory($this->_cacheDirectory);
    }

    public function testCache()
    {
        mkdir($this->_cacheDirectory);

        $this->_mockBootConfigService->shouldReceive('isCacheEnabledForElement')->times(3)->with('ioc-container')->andReturn(true);
        $this->_mockBootConfigService->shouldReceive('isCacheKillerTurnedOn')->twice()->andReturn(false);
        $this->_mockBootConfigService->shouldReceive('getAbsolutePathToCacheFileForElement')->with('ioc-container')->andReturn($this->_cacheDirectory . '/ioc-container.php');

        $this->_mockIocContainer->shouldReceive('compile')->once();
        $this->_mockIocContainer->shouldReceive('getDelegateIconicContainerBuilder')->once()->andReturn(new ehough_iconic_ContainerBuilder());
        $this->_mockIocContainer->shouldReceive('setDelegateIconicContainerBuilder')->once()->with(ehough_mockery_Mockery::any('TubePressServiceContainer'));

        $this->_sut->compile($this->_mockIocContainer, array());

        $this->assertFileExists($this->_cacheDirectory . '/ioc-container.php');

        $this->assertFalse(class_exists('TubePressServiceContainer'));

        /** @noinspection PhpIncludeInspection */
        include $this->_cacheDirectory . '/ioc-container.php';

        $this->assertTrue(class_exists('TubePressServiceContainer'));

        /** @noinspection PhpUndefinedClassInspection */
        $this->assertTrue(new TubePressServiceContainer() instanceof ehough_iconic_ContainerBuilder);

        $this->_sut->compile($this->_mockIocContainer, array());
    }

    public function testCompile()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1IocContainerExtensions = array('FakeExtension', 'bogus class');
        $mockAddon2IocCompilerPasses = array('FakeCompilerPass', 'no such class');

        $mockAddon1->shouldReceive('getIocContainerExtensions')->once()->andReturn($mockAddon1IocContainerExtensions);
        $mockAddon1->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getIocContainerExtensions')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getIocContainerCompilerPasses')->once()->andReturn($mockAddon2IocCompilerPasses);

        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockBootConfigService->shouldReceive('isCacheEnabledForElement')->times(2)->with('ioc-container')->andReturn(false);
        $this->_mockBootConfigService->shouldReceive('isCacheKillerTurnedOn')->once()->andReturn(false);
        $this->_mockBootConfigService->shouldReceive('getAbsolutePathToCacheFileForElement')->with('ioc-container')->andReturn($this->_cacheDirectory . '/ioc-container.php');

        $this->_mockIocContainer->shouldReceive('compile')->once();
        $this->_mockIocContainer->shouldReceive('getDelegateIconicContainerBuilder')->once()->andReturn(ehough_mockery_Mockery::mock('ehough_iconic_Container'));
        $this->_mockIocContainer->shouldReceive('addCompilerPass')->once()->with(ehough_mockery_Mockery::any('FakeCompilerPass'));
        $this->_mockIocContainer->shouldReceive('registerExtension')->once()->with(ehough_mockery_Mockery::any('FakeExtension'));

        $this->_sut->compile($this->_mockIocContainer, $mockAddons);

        $this->assertTrue(true);
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }
}