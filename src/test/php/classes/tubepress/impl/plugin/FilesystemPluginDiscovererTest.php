<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . '/../../../../../resources/plugins/FakeExtension.php';


class tubepress_impl_player_FilesystemPluginDiscovererTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockFilesystemFinderFactory;

    private $_mockFinder;

    private $_fakePluginRoot;

    private $_splInfoArray;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_plugin_FilesystemPluginDiscoverer();

        $this->_fakePluginRoot = __DIR__ . '/../../../../../resources/plugins';

        $this->_mockFinder = Mockery::mock('ehough_fimble_api_Finder');

        $this->_splInfoArray = array();

        $this->_mockFinder->shouldReceive('files')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('name')->with('*.info')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('depth')->once()->with('< 3')->andReturnUsing(array($this, '_callback'));

        $this->_mockFilesystemFinderFactory = $this->createMockSingletonService('ehough_fimble_api_FinderFactory');
        $this->_mockFilesystemFinderFactory->shouldReceive('createFinder')->andReturn($this->_mockFinder);

    }

    public function testBadInfoFile2()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/bad_info_file2/b.info');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/bad_info_file2')->andReturn($this->_mockFinder);


        $result = $this->_sut->findPluginsRecursivelyInDirectory($this->_fakePluginRoot . '/bad_info_file2');

        $this->assertTrue(is_array($result));

        $this->assertTrue(count($result) === 0);
    }

    public function testBadInfoFile()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/bad_info_file/b.info');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/bad_info_file')->andReturn($this->_mockFinder);

        $result = $this->_sut->findPluginsRecursivelyInDirectory($this->_fakePluginRoot . '/bad_info_file');

        $this->assertTrue(is_array($result));

        $this->assertTrue(count($result) === 0);
    }

    public function testMismatchedPlugin()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/mismatched_plugin/x.info');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/mismatched_plugin')->andReturn($this->_mockFinder);

        $result = $this->_sut->findPluginsRecursivelyInDirectory($this->_fakePluginRoot . '/mismatched_plugin');

        $this->assertTrue(is_array($result));

        $this->assertTrue(count($result) === 1);
    }

    public function testGoodPlugin()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/good_plugin/b.info');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/good_plugin')->andReturn($this->_mockFinder);

        $result = $this->_sut->findPluginsRecursivelyInDirectory($this->_fakePluginRoot . '/good_plugin');

        $this->assertTrue(is_array($result));

        $this->assertTrue($result[0] instanceof tubepress_spi_plugin_Plugin);

        $plugin = $result[0];

        $this->assertTrue($plugin->getName() === 'Plugin B');
        $this->assertTrue($plugin->getDescription() === 'Description for plugin B');
        $this->assertTrue($plugin->getVersion() instanceof tubepress_spi_version_Version);
        $this->assertTrue((string) $plugin->getVersion() === '3.2.1');
        $this->assertEquals(array('SomethingPath'), $plugin->getPsr0ClassPathRoots());
    }

    public function _callback()
    {
        return $this->_splInfoArray;
    }
}