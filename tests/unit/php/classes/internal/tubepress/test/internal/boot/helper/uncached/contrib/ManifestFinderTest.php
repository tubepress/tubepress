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
 * @covers tubepress_internal_boot_helper_uncached_contrib_ManifestFinder<extended>
 */
class tubepress_test_internal_boot_helper_uncached_contrib_ManifestFinderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_helper_uncached_contrib_ManifestFinder
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
    private $_mockFinderFactory;

    /**
     * @var string
     */
    private $_mockSystemDirectory;

    /**
     * @var string
     */
    private $_mockUserDirectory;

    private $_mockManifest;

    public function onSetup()
    {
        $this->_mockLogger        = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockBootSettings  = $this->mock(tubepress_api_boot_BootSettingsInterface::_);
        $this->_mockFinderFactory = $this->mock('ehough_finder_FinderFactoryInterface');
        $this->_mockManifest      = array();

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->onTearDown();

        $this->_mockSystemDirectory = sys_get_temp_dir() . '/manifest-finder-test/system';
        $this->_mockUserDirectory   = sys_get_temp_dir() . '/manifest-finder-test/user';
        $created = mkdir($this->_mockUserDirectory, 0755, true) && mkdir($this->_mockSystemDirectory, 0755, true);
        $this->assertTrue($created);

        $this->_sut = new tubepress_internal_boot_helper_uncached_contrib_ManifestFinder(
            $this->_mockSystemDirectory,
            $this->_mockUserDirectory,
            'foobar.json',
            $this->_mockLogger,
            $this->_mockBootSettings,
            $this->_mockFinderFactory
        );
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/manifest-finder-test');
    }

    public function testCannotDecode()
    {
        $this->_mockLogger->shouldReceive('error')->once();

        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir-path');

        list ($handle, $path) = $this->getTemporaryFile();

        $this->_setupFilesystem($handle, $path, 'bla bla');

        $actual = $this->_sut->find();

        $this->assertTrue(is_array($actual));
        $this->assertCount(0, $actual);
    }

    public function testFindAll()
    {
        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->once()->andReturn('user-content-dir-path');

        list ($handle, $path) = $this->getTemporaryFile();

        $this->_setupFilesystem($handle, $path, json_encode(array('hi' => 'there')));

        $actual = $this->_sut->find();

        $this->assertTrue(is_array($actual));
        $this->assertCount(1, $actual);
        $keys = array_keys($actual);

        $this->assertEquals($path, $keys[0]);
        $this->assertEquals(array('hi' => 'there'), $actual[$path]);
    }

    private function _setupFilesystem($handle, $path, $contents)
    {
        fwrite($handle, $contents);

        $mockIterator = new ArrayIterator(array(

            new SplFileInfo($path),
        ));

        $mockFinder = $this->mock('ehough_finder_FinderInterface');
        $mockFinder->shouldReceive('followLinks')->once()->andReturn($mockFinder);
        $mockFinder->shouldReceive('files')->once()->andReturn($mockFinder);
        $mockFinder->shouldReceive('in')->once()->with($this->_mockSystemDirectory)->andReturn($mockFinder);
        $mockFinder->shouldReceive('name')->once()->with('foobar.json')->andReturn($mockFinder);
        $mockFinder->shouldReceive('depth')->once()->with('< 2')->andReturn($mockIterator);
        $this->_mockFinderFactory->shouldReceive('createFinder')->once()->andReturn($mockFinder);
    }
}