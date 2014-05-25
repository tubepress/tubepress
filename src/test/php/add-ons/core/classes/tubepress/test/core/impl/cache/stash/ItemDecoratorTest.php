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
 * @covers tubepress_core_impl_cache_stash_ItemDecorator
 */
class tubepress_test_core_impl_cache_stash_ItemDecoratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_cache_stash_ItemDecorator
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockItem;

    public function onSetup()
    {
        $this->_mockPool    = $this->mock('ehough_stash_interfaces_PoolInterface');
        $this->_mockItem    = $this->mock('ehough_stash_interfaces_ItemInterface');
        $this->_mockContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_sut         = new tubepress_core_impl_cache_stash_ItemDecorator($this->_mockContext, $this->_mockItem, $this->_mockPool);
    }

    public function testCacheCleaningFactor()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR)->andReturn('1');
        $this->_mockPool->shouldReceive('flush')->once();

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 88)->andReturn(true);

        $result = $this->_sut->set(array(55), 88);

        $this->assertTrue($result);
    }

    public function testSetExplicitTtl()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR)->andReturn('0');

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 88)->andReturn(true);

        $result = $this->_sut->set(array(55), 88);

        $this->assertTrue($result);
    }

    public function testSetDefaultTtl()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR)->andReturn('0');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::CACHE_LIFETIME_SECONDS)->andReturn('44');

        $this->_mockItem->shouldReceive('set')->once()->with(array(55), 44)->andReturn(true);

        $result = $this->_sut->set(array(55));

        $this->assertTrue($result);
    }

    public function testRemove()
    {
        $this->_mockItem->shouldReceive('clear')->once()->andReturn(false);

        $result = $this->_sut->clear();

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
        $this->_mockItem->shouldReceive('isMiss')->once()->andReturn(true);

        $result = $this->_sut->isMiss();

        $this->assertTrue($result);
    }

    public function testDisable()
    {
        $this->_mockItem->shouldReceive('disable')->once()->andReturn(true);

        $result = $this->_sut->disable();

        $this->assertTrue($result);
    }

    public function testIsDisabled()
    {
        $this->_mockItem->shouldReceive('isDisabled')->once()->andReturn(true);

        $result = $this->_sut->isDisabled();

        $this->assertTrue($result);
    }

    public function testLock()
    {
        $this->_mockItem->shouldReceive('lock')->once()->with(456)->andReturn(true);

        $result = $this->_sut->lock(456);

        $this->assertTrue($result);
    }

    public function testExtend()
    {
        $this->_mockItem->shouldReceive('extend')->once()->with(678)->andReturn(true);

        $result = $this->_sut->extend(678);

        $this->assertTrue($result);
    }

    public function testSetLogger()
    {
        $logger = new stdClass();
        $this->_mockItem->shouldReceive('setLogger')->once()->with($logger);

        $this->_sut->setLogger($logger);

        $this->assertTrue(true);
    }
}