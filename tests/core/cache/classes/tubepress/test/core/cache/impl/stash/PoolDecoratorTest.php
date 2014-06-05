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
 * @covers tubepress_core_cache_impl_stash_PoolDecorator
 */
class tubepress_test_core_cache_impl_stash_PoolDecoratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_cache_impl_stash_PoolDecorator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPool;

    public function onSetup()
    {
        $this->_mockPool    = $this->mock('ehough_stash_interfaces_PoolInterface');
        $this->_mockContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_sut         = new tubepress_core_cache_impl_stash_PoolDecorator($this->_mockContext, $this->_mockPool);
    }

    public function testGetItemIterator()
    {
        $this->_mockPool->shouldReceive('getItem')->once()->andReturn($this->mock('ehough_stash_interfaces_ItemInterface'));

        $item = $this->_sut->getItemIterator(array('foo'));

        $this->assertInstanceOf('ArrayIterator', $item);
        $this->assertTrue($item->count() === 1);
    }

    public function testGetItem()
    {
        $this->_mockPool->shouldReceive('getItem')->once()->andReturn($this->mock('ehough_stash_interfaces_ItemInterface'));

        $item = $this->_sut->getItem('foo');

        $this->assertInstanceOf('tubepress_core_cache_impl_stash_ItemDecorator', $item);
    }

    public function testClear()
    {
        $this->_mockPool->shouldReceive('flush')->once()->andReturn(true);

        $this->assertTrue($this->_sut->flush());
    }

    public function testPurge()
    {
        $this->_mockPool->shouldReceive('purge')->once();

        $this->_sut->purge();

        $this->assertTrue(true);
    }

    public function testSetDriver()
    {
        $driver = $this->mock('ehough_stash_interfaces_DriverInterface');

        $this->_mockPool->shouldReceive('setDriver')->once()->with($driver);

        $this->_sut->setDriver($driver);

        $this->_mockPool->shouldReceive('getDriver')->once()->andReturn($driver);

        $this->assertSame($driver, $this->_sut->getDriver());
    }

    public function testSetLogger()
    {
        $logger = new stdClass();
        $this->_mockPool->shouldReceive('setLogger')->once()->with($logger);

        $this->_sut->setLogger($logger);

        $this->assertTrue(true);
    }
}