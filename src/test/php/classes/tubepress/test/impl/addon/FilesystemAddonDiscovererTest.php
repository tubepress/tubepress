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

require_once __DIR__ . '/../../../../../../resources/addons/FakeExtension.php';

class tubepress_test_impl_addon_FilesystemAddonDiscovererTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_FilesystemAddonDiscoverer
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilesystemFinderFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinder;

    private $_fakeAddonRoot;

    private $_splInfoArray;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_addon_FilesystemAddonDiscoverer();

        $this->_fakeAddonRoot = realpath(__DIR__ . '/../../../../../../resources/addons');

        $this->_mockFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');

        $this->_splInfoArray = array();

        $this->_mockFinder->shouldReceive('followLinks')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('files')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('name')->with('*.json')->andReturn($this->_mockFinder);
        $this->_mockFinder->shouldReceive('depth')->once()->with(0)->andReturnUsing(array($this, '_callback'));

        $this->_mockFilesystemFinderFactory = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockFilesystemFinderFactory->shouldReceive('createFinder')->andReturn($this->_mockFinder);
    }

    public function testBadManifest()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/bad_manifestsyntax/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakeAddonRoot . '/bad_manifestsyntax')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakeAddonRoot . '/bad_manifestsyntax');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testBadVersion()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/bad_version/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakeAddonRoot . '/bad_version')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakeAddonRoot . '/bad_version');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testBadName()
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/bad_name/b.json');
        $this->_mockFinder->shouldReceive('in')->with($this->_fakeAddonRoot . '/bad_name')->andReturn($this->_mockFinder);
        $result = $this->_sut->findAddonsInDirectory($this->_fakeAddonRoot . '/bad_name');
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

    public function testGoodAddon()
    {
        $addon = $this->_verifyGoodAddon('good_addon');

        $this->assertEquals('tubepress_test_impl_addon_FilesystemAddonDiscovererTest', $addon->getBootstrap());
    }

    public function testGoodAddon2()
    {
        $addon = $this->_verifyGoodAddon('good_addon2');

        $this->assertEquals(TUBEPRESS_ROOT . '/src/test/resources/addons/good_addon2/some/dir/boot.php', $addon->getBootstrap());
    }

    private function _verifyGoodAddon($dir)
    {
        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/' . $dir . '/b.json');

        $this->_mockFinder->shouldReceive('in')->with($this->_fakeAddonRoot . '/' . $dir)->andReturn($this->_mockFinder);

        $result = $this->_sut->findAddonsInDirectory($this->_fakeAddonRoot . '/' . $dir);

        $this->assertTrue(is_array($result));
        $this->assertTrue(!empty($result));

        $this->assertTrue($result[0] instanceof tubepress_spi_addon_Addon);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $result[0];

        $this->assertTrue($addon->getName() === 'addon-b');
        $this->assertTrue($addon->getTitle() === 'Title for Add-on B');
        $this->assertTrue($addon->getVersion() instanceof tubepress_spi_version_Version);
        $this->assertTrue((string) $addon->getVersion() === '3.2.1');
        $this->assertTrue(count($addon->getLicenses()) === 2);

        $licenses = $addon->getLicenses();

        $this->assertEquals('http://foo.bar', $licenses[0]['url']);
        $this->assertEquals('http://foo.com', $licenses[1]['url']);

        $author = $addon->getAuthor();

        $this->assertEquals('Eric Hough', $author['name']);
        $this->assertEquals('This is a description', $addon->getDescription());
        $this->assertEquals(array('one', 'three', 'two'), $addon->getKeywords());
        $this->assertEquals('https://some.thing', $addon->getHomepageUrl());
        $this->assertEquals('http://hel.lo', $addon->getDocumentationUrl());
        $this->assertEquals('http://some.demo', $addon->getDemoUrl());
        $this->assertEquals('http://down.load', $addon->getDownloadUrl());
        $this->assertEquals('https://bug.tracker', $addon->getBugTrackerUrl());
        $this->assertEquals(array('foobar' => TUBEPRESS_ROOT . '/src/test/resources/addons/' . $dir . '//foo/bar',
            'foozbaz' => TUBEPRESS_ROOT . '/src/test/resources/addons/' . $dir . '//fooz/baz'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('yellow', 'orange'), $addon->getIocContainerCompilerPasses());
        $this->assertEquals(array('blue', 'black'), $addon->getIocContainerExtensions());

        return $addon;
    }

    public function _callback()
    {
        return $this->_splInfoArray;
    }

    public function boot()
    {

    }
}