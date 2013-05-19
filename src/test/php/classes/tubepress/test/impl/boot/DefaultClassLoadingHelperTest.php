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
 * @covers tubepress_impl_boot_DefaultClassLoadingHelper<extended>
 */
class tubepress_test_impl_boot_DefaultClassLoadingHelperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_DefaultClassLoadingHelper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootConfigService;

    private $_cacheDirectory;

    public function onSetup()
    {
        $this->_sut                   = new tubepress_impl_boot_DefaultClassLoadingHelper();
        $this->_mockClassLoader       = ehough_mockery_Mockery::mock('ehough_pulsar_ComposerClassLoader');
        $this->_mockBootConfigService = $this->createMockSingletonService(tubepress_spi_boot_BootConfigService::_);
        $this->_cacheDirectory        = sys_get_temp_dir() . '/tubepress-test-' . mt_rand();
    }

    public function onTearDown()
    {
        $this->deleteDirectory($this->_cacheDirectory);
    }

    public function testCacheMissThenHit()
    {
        mkdir($this->_cacheDirectory);

        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1->shouldReceive('getPsr0ClassPathRoots')->twice()->andReturn(array('some root', 'prefix' => 'something'));
        $mockAddon2->shouldReceive('getPsr0ClassPathRoots')->twice()->andReturn(array());
        $mockAddon1->shouldReceive('getClassMap')->twice()->andReturn(array());
        $mockAddon2->shouldReceive('getClassMap')->twice()->andReturn(array('foo' => 'bar'));
        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockBootConfigService->shouldReceive('isCacheEnabledForElement')->times(2)->with('classloader')->andReturn(true);
        $this->_mockBootConfigService->shouldReceive('getAbsolutePathToCacheFileForElement')->with('classloader')->andReturn($this->_cacheDirectory . '/classloader.txt');

        $this->_sut->addClassHintsForAddons($mockAddons, new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor'));

        $this->assertFileExists($this->_cacheDirectory . '/classloader.txt');

        $contents = file_get_contents($this->_cacheDirectory . '/classloader.txt');

        $this->assertTrue($contents !== false);

        $deserialized = unserialize($contents);

        $this->assertTrue($deserialized instanceof ehough_pulsar_ComposerClassLoader);

        $this->_sut->addClassHintsForAddons($mockAddons, new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor'));
    }

    public function testPrime()
    {
        $this->_mockBootConfigService->shouldReceive('isCacheKillerTurnedOn')->once()->andReturn(false);

        $this->_mockBootConfigService->shouldReceive('isCacheEnabledForElement')->times(1)->with('classloader')->andReturn(false);

        $this->_sut->prime($this->_mockClassLoader);

        $this->assertTrue(true);
    }

    public function testAddClassHints()
    {
        $mockAddon1 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon2 = ehough_mockery_Mockery::mock(tubepress_spi_addon_Addon::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array('some root', 'prefix' => 'something'));
        $mockAddon2->shouldReceive('getPsr0ClassPathRoots')->once()->andReturn(array());
        $mockAddon1->shouldReceive('getClassMap')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getClassMap')->once()->andReturn(array('foo' => 'bar'));
        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockClassLoader->shouldReceive('registerNamespaceFallback')->once()->with('some root');
        $this->_mockClassLoader->shouldReceive('registerPrefixFallback')->once()->with('some root');

        $this->_mockClassLoader->shouldReceive('registerPrefix')->once()->with('prefix', 'something');
        $this->_mockClassLoader->shouldReceive('registerNamespace')->once()->with('prefix', 'something');

        $this->_mockBootConfigService->shouldReceive('isCacheEnabledForElement')->times(1)->with('classloader')->andReturn(false);
        $this->_mockBootConfigService->shouldReceive('getAbsolutePathToCacheFileForElement')->with('classloader')->andReturn($this->_cacheDirectory . '/classloader.txt');

        $this->_sut->addClassHintsForAddons($mockAddons, $this->_mockClassLoader);

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