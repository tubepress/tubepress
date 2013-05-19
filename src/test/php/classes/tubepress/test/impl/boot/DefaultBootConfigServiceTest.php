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
 * @covers tubepress_impl_boot_DefaultBootConfigService<extended>
 */
class tubepress_test_impl_boot_DefaultBootConfigServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_DefaultBootConfigService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut                     = new tubepress_impl_boot_DefaultBootConfigService();
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function onTearDown()
    {
        unset($_GET['tubepress_kill_cache_boot']);
    }

    public function testInvalidElement()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn(TUBEPRESS_ROOT . '/src/test/resources/boot-configs/one');

        $this->setExpectedException('InvalidArgumentException', 'Invalid boot config element: xyz');

        $this->_sut->getAbsolutePathToCacheFileForElement('xyz');
    }

    public function testCacheKiller()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn(TUBEPRESS_ROOT . '/src/test/resources/boot-configs/one');

        $this->assertFalse($this->_sut->isCacheKillerTurnedOn());

        $_GET['some-param'] = 'true';

        $this->assertTrue($this->_sut->isCacheKillerTurnedOn());
    }

    public function testMissing()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn(TUBEPRESS_ROOT . '/src/test/resources/boot-configs/three');

        $this->assertDefaults();
    }

    public function testBad()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn(TUBEPRESS_ROOT . '/src/test/resources/boot-configs/two');

        $this->assertDefaults();
    }

    public function testNormal()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn(TUBEPRESS_ROOT . '/src/test/resources/boot-configs/one');

        $result = $this->_sut->getAbsolutePathToCacheFileForElement('add-ons');
        $this->assertEquals('/foo/serialized-addons.txt', $result);

        $result = $this->_sut->getAbsolutePathToCacheFileForElement('ioc-container');
        $this->assertEquals('/foo/cached-ioc-container.php', $result);

        $result = $this->_sut->getAddonBlacklistArray();
        $this->assertEquals(array('one', 'two', 'three'), $result);

        $this->assertTrue($this->_sut->isCacheEnabledForElement('add-ons'));
        $this->assertFalse($this->_sut->isCacheEnabledForElement('ioc-container'));
    }

    private function assertDefaults()
    {
        $result = $this->_sut->getAbsolutePathToCacheFileForElement('add-ons');
        $this->assertRegExp('~^/tmp/[^/]+/serialized-addons\.txt$~', $result);

        $result = $this->_sut->getAbsolutePathToCacheFileForElement('ioc-container');
        $this->assertRegExp('~^/tmp/[^/]+/cached-ioc-container\.php~', $result);

        $result = $this->_sut->getAddonBlacklistArray();
        $this->assertEquals(array(), $result);

        $this->assertFalse($this->_sut->isCacheEnabledForElement('add-ons'));
        $this->assertFalse($this->_sut->isCacheEnabledForElement('ioc-container'));
    }
}