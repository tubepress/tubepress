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
 * @covers tubepress_core_provider_impl_ItemSorter<extended>
 */
class tubepress_test_core_provider_impl_ItemSorterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_provider_impl_ItemSorter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFirstItem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSecondItem;

    public function onSetup()
    {
        $this->_sut = new tubepress_core_provider_impl_ItemSorter();
        $this->_mockFirstItem = $this->mock('tubepress_core_provider_api_MediaItem');
        $this->_mockSecondItem = $this->mock('tubepress_core_provider_api_MediaItem');
    }

    /**
     * @dataProvider getDataNumericSort
     */
    public function testNumericSort($firstValue, $secondValue, $desc, $expectedResult)
    {
        $this->_mockFirstItem->shouldReceive('hasAttribute')->once()->with('x')->andReturn(true);
        $this->_mockFirstItem->shouldReceive('getAttribute')->once()->with('x')->andReturn($firstValue);
        $this->_mockSecondItem->shouldReceive('hasAttribute')->once()->with('x')->andReturn(true);
        $this->_mockSecondItem->shouldReceive('getAttribute')->once()->with('x')->andReturn($secondValue);

        $result = $this->_sut->numericSort(
            $this->_mockFirstItem,
            $this->_mockSecondItem,
            'x',
            $desc
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function getDataNumericSort()
    {
        return array(

            array(1, 2, false, -1),
            array(1, 2, true, 1),
            array(1, 1, true, 0),
            array('1', '2', false, -1),
            array('1', '2', true, 1),
            array('1', '1', true, 0),
            array(1.0, 2, false, -1),
            array(1.0, 2, true, 1),
            array(1.0, 1, true, 0),
        );
    }

    public function testNumericSortSecondDoesNotHaveAttribute()
    {
        $this->_mockFirstItem->shouldReceive('hasAttribute')->once()->with('x')->andReturn(true);
        $this->_mockSecondItem->shouldReceive('hasAttribute')->once()->with('x')->andReturn(false);

        $result = $this->_sut->numericSort(
            $this->_mockFirstItem,
            $this->_mockSecondItem,
            'x',
            false
        );

        $this->assertEquals(0, $result);
    }

    public function testNumericSortFirstDoesNotHaveAttribute()
    {
        $this->_mockFirstItem->shouldReceive('hasAttribute')->once()->with('x')->andReturn(false);

        $result = $this->_sut->numericSort(
            $this->_mockFirstItem,
            $this->_mockSecondItem,
            'x',
            false
        );

        $this->assertEquals(0, $result);
    }
}