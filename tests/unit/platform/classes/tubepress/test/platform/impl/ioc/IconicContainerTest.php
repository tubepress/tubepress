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
 * @covers tubepress_platform_impl_ioc_Container
 */
class tubepress_test_impl_ioc_IconicContainerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_ioc_Container
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_mockContainer = $this->mock('ehough_iconic_ContainerInterface');
        $this->_sut           = new tubepress_platform_impl_ioc_Container($this->_mockContainer);
    }

    public function testHasParameter()
    {
        $this->_mockContainer->shouldReceive('hasParameter')->once()->with('foo')->andReturn(false);
        $result = $this->_sut->hasParameter('foo');
        $this->assertFalse($result);
    }

    public function testSetParam()
    {
        $this->_mockContainer->shouldReceive('setParameter')->once()->with('foo', 'bar');
        $this->_sut->setParameter('foo', 'bar');
        $this->assertTrue(true);
    }

    public function testSet()
    {
        $bla = new stdClass();
        $this->_mockContainer->shouldReceive('set')->once()->with('foo', $bla);
        $this->_sut->set('foo', $bla);
        $this->assertTrue(true);
    }

    public function testHas()
    {
        $this->_mockContainer->shouldReceive('has')->once()->with('foo')->andReturn(false);
        $result = $this->_sut->has('foo');
        $this->assertFalse($result);
    }

    public function testGetParam()
    {
        $this->_mockContainer->shouldReceive('getParameter')->once()->with('foo')->andReturn('bar');
        $result = $this->_sut->getParameter('foo');
        $this->assertEquals('bar', $result);
    }

    public function testGet()
    {
        $bla = new stdClass();
        $this->_mockContainer->shouldReceive('get')->once()->with('foo', ehough_iconic_ContainerInterface::NULL_ON_INVALID_REFERENCE)->andReturn($bla);
        $result = $this->_sut->get('foo');
        $this->assertSame($bla, $result);
    }
}
