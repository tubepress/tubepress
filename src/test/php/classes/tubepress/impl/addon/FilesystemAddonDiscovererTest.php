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

require_once __DIR__ . '/../../../../../resources/plugins/FakeExtension.php';

class tubepress_impl_addon_FilesystemAddonDiscovererTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_FilesystemAddonDiscoverer
     */
    private $_sut;

    private $_mockFilesystemFinderFactory;

    private $_mockFinder;

    private $_fakePluginRoot;

    private $_splInfoArray;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_addon_FilesystemAddonDiscoverer();

        $this->_fakePluginRoot = realpath(__DIR__ . '/../../../../../resources/plugins');

        $this->_mockFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');

        $this->_splInfoArray = array();

        $this->_mockFinder->shouldReceive('files')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('name')->with('*.json')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('depth')->once()->with(0)->andReturnUsing(array($this, '_callback'));

        $this->_mockFilesystemFinderFactory = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockFilesystemFinderFactory->shouldReceive('createFinder')->andReturn($this->_mockFinder);
    }

    public function testBadManifest()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/bad_manifestsyntax/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/bad_manifestsyntax')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakePluginRoot . '/bad_manifestsyntax');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testBadVersion()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/bad_version/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/bad_version')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakePluginRoot . '/bad_version');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testBadName()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/bad_name/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/bad_name')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakePluginRoot . '/bad_name');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testGoodPlugin()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakePluginRoot . '/good_plugin/b.json');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakePluginRoot . '/good_plugin')->andReturn($this->_mockFinder);

        $result = $this->_sut->findAddonsInDirectory($this->_fakePluginRoot . '/good_plugin');

        $this->assertTrue(is_array($result));
        $this->assertTrue(!empty($result));

        $this->assertTrue($result[0] instanceof tubepress_spi_addon_Addon);

        /**
         * @var $plugin tubepress_spi_addon_Addon
         */
        $plugin = $result[0];

        $this->assertTrue($plugin->getName() === 'plugin-b');
        $this->assertTrue($plugin->getTitle() === 'Title for Plugin B');
        $this->assertTrue($plugin->getVersion() instanceof tubepress_spi_version_Version);
        $this->assertTrue((string) $plugin->getVersion() === '3.2.1');
        $this->assertTrue(count($plugin->getLicenses()) === 2);
        $this->assertEquals('http://foo.bar', $plugin->getLicenses()[0]['url']);
        $this->assertEquals('http://foo.com', $plugin->getLicenses()[1]['url']);
        $this->assertEquals('tubepress_impl_addon_FilesystemAddonDiscovererTest', $plugin->getBootstrap());
        $this->assertEquals('Eric Hough', $plugin->getAuthor()['name']);
        $this->assertEquals('This is a description', $plugin->getDescription());
        $this->assertEquals(array('one', 'three', 'two'), $plugin->getKeywords());
        $this->assertEquals('https://some.thing', $plugin->getHomepageUrl());
        $this->assertEquals('http://hel.lo', $plugin->getDocumentationUrl());
        $this->assertEquals('http://some.demo', $plugin->getDemoUrl());
        $this->assertEquals('http://down.load', $plugin->getDownloadUrl());
        $this->assertEquals('https://bug.tracker', $plugin->getBugTrackerUrl());
        $this->assertEquals(array('/foo/bar', '/fooz/baz'), $plugin->getPsr0ClassPathRoots());
        $this->assertEquals(array('yellow', 'orange'), $plugin->getIocContainerCompilerPasses());
        $this->assertEquals(array('blue', 'black'), $plugin->getIocContainerExtensions());
    }

    public function _callback()
    {
        return $this->_splInfoArray;
    }

    public function boot()
    {

    }
}