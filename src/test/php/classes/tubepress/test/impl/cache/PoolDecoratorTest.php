<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_cache_PoolDecoratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_cache_PoolDecorator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPool;

    public function onSetup()
    {
        $this->_mockPool = ehough_mockery_Mockery::mock('ehough_stash_interfaces_PoolInterface');

        $this->_sut = new tubepress_impl_cache_PoolDecorator($this->_mockPool);

        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    public function testGetItemIterator()
    {
        $this->_mockPool->shouldReceive('getItem')->once()->andReturn(ehough_mockery_Mockery::mock('ehough_stash_interfaces_ItemInterface'));

        $item = $this->_sut->getItemIterator(array('foo'));

        $this->assertInstanceOf('ArrayIterator', $item);
        $this->assertTrue($item->count() === 1);
    }

    public function testGetItem()
    {
        $this->_mockPool->shouldReceive('getItem')->once()->andReturn(ehough_mockery_Mockery::mock('ehough_stash_interfaces_ItemInterface'));

        $item = $this->_sut->getItem('foo');

        $this->assertInstanceOf('tubepress_impl_cache_ItemDecorator', $item);
    }

    public function testClear()
    {
        $this->_mockPool->shouldReceive('flush')->once()->andReturn(true);

        $this->assertTrue($this->_sut->flush());
    }
}