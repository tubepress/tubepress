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
 * @covers tubepress_impl_addon_Registry<extended>
 */
class tubepress_test_impl_addon_RegistryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_addon_Registry
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
    private $_mockBootSettings;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    private $_fakeAddonRoot;

    private $_splInfoArray;

    public function onSetup()
    {
        $this->_mockFinderFactory       = $this->mock('ehough_finder_FinderFactoryInterface');
        $this->_mockBootSettings        = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockSystemFinder        = $this->mock('ehough_finder_FinderInterface');
        $this->_mockUserFinder          = $this->mock('ehough_finder_FinderInterface');
        $this->_fakeAddonRoot           = realpath(TUBEPRESS_ROOT . '/src/test/platform/fixtures/classes/tubepress/test/impl/addon/registrytests');
        $this->_splInfoArray            = array();

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_impl_addon_Registry(

            $this->_mockLogger,
            $this->_mockBootSettings,
            $this->_mockFinderFactory
        );

        $this->_mockUserFinder->name = 'user finder';
        $this->_mockSystemFinder->name = 'system finder';

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($this->_mockSystemFinder, $this->_mockUserFinder);

        $this->_mockSystemFinder->shouldReceive('followLinks')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('files')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('name')->with('manifest.json')->andReturn($this->_mockSystemFinder);
        $this->_mockSystemFinder->shouldReceive('depth')->once()->with('< 2')->andReturn(array());

        $this->_mockUserFinder->shouldReceive('followLinks')->andReturn($this->_mockUserFinder);
        $this->_mockUserFinder->shouldReceive('files')->andReturn($this->_mockUserFinder);
        $this->_mockUserFinder->shouldReceive('name')->with('manifest.json')->andReturn($this->_mockUserFinder);
        $this->_mockUserFinder->shouldReceive('depth')->once()->with('< 2')->andReturnUsing(array($this, '_callback'));
    }

    public function testBadManifest()
    {
        $this->_mockBootSettings->shouldReceive('getAddonBlacklistArray')->twice()->andReturn(array());

        $this->_mockLogger->shouldReceive('error')->once()->with(sprintf('Decoded manifest at %s/src/test/platform/fixtures/classes/tubepress/test/impl/addon/registrytests/bad_manifestsyntax/b.json? no', TUBEPRESS_ROOT));

        $this->_verifyBadAddon('bad_manifestsyntax');
    }

    public function testCacheKiller()
    {
        $this->_mockBootSettings->shouldReceive('getAddonBlacklistArray')->twice()->andReturn(array());

        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/good_addon');

        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/good_addon/add-ons/something/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/good_addon/add-ons')->andReturn($this->_mockUserFinder);

        $results = $this->_sut->getAll();

        $this->assertTrue(is_array($results));
        $this->assertTrue(!empty($results));

        $this->assertTrue($results[0] instanceof tubepress_api_addon_AddonInterface);
    }

    public function testBadName()
    {
        $this->_mockBootSettings->shouldReceive('getAddonBlacklistArray')->twice()->andReturn(array());
        $this->_mockLogger->shouldReceive('error')->once()->with(sprintf('Decoded manifest at %s/src/test/platform/fixtures/classes/tubepress/test/impl/addon/registrytests/bad_name/b.json? no', TUBEPRESS_ROOT));

        $this->_verifyBadAddon('bad_name');
    }

    public function testGoodAddon()
    {
        $this->_mockBootSettings->shouldReceive('getAddonBlacklistArray')->twice()->andReturn(array());

        $this->_verifyGoodAddon('good_addon', $this->_fakeAddonRoot . '/good_addon/add-ons/something');
    }

    public function testGoodAddon2()
    {
        $this->_mockBootSettings->shouldReceive('getAddonBlacklistArray')->twice()->andReturn(array());

        $this->_verifyGoodAddon('good_addon2', $this->_fakeAddonRoot . '/good_addon2/add-ons');
    }

    public function _callback()
    {
        return $this->_splInfoArray;
    }

    private function _verifyGoodAddon($dir, $jsonFilePath)
    {
        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/' . $dir);

        $this->_splInfoArray[] = new SplFileInfo($jsonFilePath . '/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/' . $dir . '/add-ons')->andReturn($this->_mockUserFinder);

        $result = $this->_sut->getAll();

        $this->assertTrue(is_array($result));
        $this->assertTrue(!empty($result));

        $this->assertTrue($result[0] instanceof tubepress_api_addon_AddonInterface);

        /**
         * @var $addon tubepress_api_addon_AddonInterface
         */
        $addon = $result[0];

        $this->assertTrue($addon->getName() === 'addon-b');
        $this->assertTrue($addon->getTitle() === 'Title for Add-on B');
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
        $this->assertEquals(array('yellow' => 20, 'orange' => 40), $addon->getMapOfCompilerPassClassNamesToPriorities());
        $this->assertEquals(array('blue', 'black'), $addon->getExtensionClassNames());

        return $addon;
    }

    private function _verifyBadAddon($dir)
    {
        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn($this->_fakeAddonRoot . '/' . $dir);

        $this->_splInfoArray[] = new SplFileInfo($this->_fakeAddonRoot . '/' . $dir . '/b.json');

        $this->_mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/add-ons')->andReturn($this->_mockSystemFinder);
        $this->_mockUserFinder->shouldReceive('in')->once()->with($this->_fakeAddonRoot . '/' . $dir . '/add-ons')->andReturn($this->_mockUserFinder);

        $result = $this->_sut->getAll();

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }
}