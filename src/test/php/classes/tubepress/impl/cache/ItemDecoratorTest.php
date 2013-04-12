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
class tubepress_impl_cache_ItemDecoratorTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_cache_ItemDecorator
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockItem;

    public function onSetup()
    {
        $this->_mockPool = ehough_mockery_Mockery::mock('ehough_stash_PoolInterface');
        $this->_mockItem = ehough_mockery_Mockery::mock('ehough_stash_ItemInterface');

        $this->_sut = new tubepress_impl_cache_ItemDecorator($this->_mockItem, $this->_mockPool);

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    public function testCacheCleaningFactor()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR)->andReturn('1');
        $this->_mockPool->shouldReceive('clear')->once();

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 88)->andReturn(true);

        $result = $this->_sut->set(array(55), 88);

        $this->assertTrue($result);
    }

    public function testSetExplicitTtl()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR)->andReturn('0');

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 88)->andReturn(true);

        $result = $this->_sut->set(array(55), 88);

        $this->assertTrue($result);
    }

    public function testSetDefaultTtl()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR)->andReturn('0');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS)->andReturn('44');

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 44)->andReturn(true);

        $result = $this->_sut->set(array(55));

        $this->assertTrue($result);
    }

    public function testRemove()
    {
        $this->_mockItem->shouldReceive('remove')->once()->andReturn(false);

        $result = $this->_sut->remove();

        $this->assertFalse($result);
    }

    public function testGet()
    {
        $this->_mockItem->shouldReceive('get')->once()->andReturn(array(33));

        $result = $this->_sut->get();

        $this->assertEquals(array(33), $result);
    }

    public function testGetKey()
    {
        $this->_mockItem->shouldReceive('getKey')->once()->andReturn('bar');

        $result = $this->_sut->getKey();

        $this->assertEquals('bar', $result);
    }

    public function testIsValid()
    {
        $this->_mockItem->shouldReceive('isValid')->once()->andReturn(true);

        $result = $this->_sut->isValid();

        $this->assertTrue($result);
    }
}