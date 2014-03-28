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
 * @covers tubepress_impl_addon_AddonFinder<extended>
 */
class tubepress_test_impl_boot_secondary_AddonDiscoveryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_AddonFinder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSystemFinder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUserFinder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    private $_fakeAddonRoot;

    private $_splInfoArray;

    public function onSetup()
    {
        $this->_mockFinderFactory       = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockSystemFinder        = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $this->_mockUserFinder          = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $this->_fakeAddonRoot           = realpath(TUBEPRESS_ROOT . '/src/test/resources/fixtures/classes/tubepress/test/impl/boot/defaultaddondiscoverer/add-ons');
        $this->_splInfoArray            = array();
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut                     = new tubepress_impl_addon_AddonFinder(

            $this->_mockFinderFactory,
            $this->_mockEnvironmentDetector
        );

        $this->_mockUserFinder->name = 'user finder';
        $this->_mockSystemFinder->name = 'system finder';

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($this->_mockSystemFinder, $this->_mockUserFinder);

        $this->_mockSystemFinder->shouldReceive('followLinks')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('files')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('name')->with('*.json')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('depth')->once()->with('< 2')->andReturn(array());
        $this->_mockUserFinder->shouldReceive('followLinks')->andReturn($this->_mockUserFinder);

        $this->_mockUserFinder->shouldReceive('files')->andReturn($this->_mockUserFinder);
        $this->_mockUserFinder->shouldReceive('name')->with('*.json')->andReturn($this->_mockUserFinder);
        $this->_mockUserFinder->shouldReceive('depth')->once()->with('< 2')->andReturnUsing(array($this, '_callback'));
    }

    public function testBadManifest()
    {
        $this->_verifyBadAddon('bad_manifestsyntax');
    }

    public function testCacheKiller()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/good_addon');

        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/good_addon/add-ons/something/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/php/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/good_addon/add-ons')->andReturn($this->_mockUserFinder);

        $results = $this->_sut->findAddons(array());

        $this->assertTrue(is_array($results));
        $this->assertTrue(!empty($results));

        $this->assertTrue($results[0] instanceof tubepress_spi_addon_AddonInterface);
    }

    public function testBadVersion()
    {
        $this->_verifyBadAddon('bad_version');
    }

    public function testBadName()
    {
        $this->_verifyBadAddon('bad_name');
    }

    public function testGoodAddon()
    {
        $this->_verifyGoodAddon('good_addon', $this->_fakeAddonRoot . '/good_addon/add-ons/something');
    }

    public function testGoodAddon2()
    {
        $this->_verifyGoodAddon('good_addon2', $this->_fakeAddonRoot . '/good_addon2/add-ons');
    }

    public function _callback()
    {
        return $this->_splInfoArray;
    }

    private function _verifyGoodAddon($dir, $jsonFilePath)
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/' . $dir);

        $this->_splInfoArray[] = new SplFileInfo($jsonFilePath . '/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/php/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/' . $dir . '/add-ons')->andReturn($this->_mockUserFinder);

        $result = $this->_sut->findAddons(array());

        $this->assertTrue(is_array($result));
        $this->assertTrue(!empty($result));

        $this->assertTrue($result[0] instanceof tubepress_spi_addon_AddonInterface);

        /**
         * @var $addon tubepress_spi_addon_AddonInterface
         */
        $addon = $result[0];

        $this->assertTrue($addon->getName() === 'addon-b');
        $this->assertTrue($addon->getTitle() === 'Title for Add-on B');
        $this->assertTrue($addon->getVersion() instanceof tubepress_spi_version_Version);
        $this->assertTrue((string) $addon->getVersion() === '3.2.1');
        $this->assertTrue(count($addon->getLicenses()) === 2);

        $licenses = $addon->getLicenses();

        $this->assertEquals('foo', $licenses[0]['type']);
        $this->assertEquals('bar', $licenses[1]['type']);

        $author = $addon->getAuthor();

        $this->assertEquals('Eric Hough', $author['name']);
        $this->assertEquals('This is a description', $addon->getDescription());
        $this->assertEquals(array('one', 'three', 'two'), $addon->getKeywords());
        $this->assertEquals('https://some.thing', $addon->getHomepageUrl());
        $this->assertEquals('http://hel.lo', $addon->getDocumentationUrl());
        $this->assertEquals('http://some.demo', $addon->getDemoUrl());
        $this->assertEquals('http://down.load', $addon->getDownloadUrl());
        $this->assertEquals('https://bug.tracker', $addon->getBugTrackerUrl());
        $this->assertEquals(array('foobar' => $jsonFilePath . '//foo/bar',
            'foozbaz' => $jsonFilePath . '//fooz/baz'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('yellow', 'orange'), $addon->getIocContainerCompilerPasses());
        $this->assertEquals(array('blue', 'black'), $addon->getIocContainerExtensions());

        return $addon;
    }

    private function _verifyBadAddon($dir)
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/' . $dir);

        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/' . $dir . '/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/php/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/' . $dir . '/add-ons')->andReturn($this->_mockUserFinder);

        $result = $this->_sut->findAddons(array());

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }
}