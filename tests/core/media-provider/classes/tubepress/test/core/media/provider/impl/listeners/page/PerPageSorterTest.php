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
 * @covers tubepress_core_media_provider_impl_listeners_page_PerPageSorter
 */
class tubepress_test_core_media_provider_impl_listeners_page_PerPageSorterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_provider_impl_listeners_page_PerPageSorter
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPage;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockItemArray;



    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockPage             = $this->mock('tubepress_core_media_provider_api_Page');
        $this->_mockItemArray        = $this->_buildMockItemArray();

        $this->_mockPage->shouldReceive('getItems')->andReturn($this->_mockItemArray);
        $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->_mockPage);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_sut = new tubepress_core_media_provider_impl_listeners_page_PerPageSorter($this->_mockLogger, $this->_mockExecutionContext);

    }

    public function testProviderSort()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, 'sortname');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest();
    }

    public function testSortNobodyCanHandle()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, 'x');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest();
    }

    public function testSortOrderNone()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_NONE);

        $this->_runTest();
    }

    public function testRandom()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_RANDOM);

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest();
    }

    private function _runTest()
    {
        $this->_sut->onVideoGalleryPage($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _setSortAndPerPageOrder($feed, $perPage)
    {
        $this->_mockExecutionContext->shouldReceive('get')->with(tubepress_core_media_provider_api_Constants::OPTION_ORDER_BY)->andReturn($feed);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_PER_PAGE_SORT)->andReturn($perPage);
    }

    private function _buildMockItemArray()
    {
        $toReturn = array();

        $mockItem1 = $this->mock('tubepress_core_media_item_api_MediaItem');
        $mockItem2 = $this->mock('tubepress_core_media_item_api_MediaItem');
        $mockItem3 = $this->mock('tubepress_core_media_item_api_MediaItem');

        $mockProvider1 = $this->mock('tubepress_core_media_provider_api_MediaProviderInterface');
        $mockProvider2 = $this->mock('tubepress_core_media_provider_api_MediaProviderInterface');

        $mockItem1->shouldReceive('getAttribute')->with(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER)->andReturn($mockProvider1);
        $mockItem2->shouldReceive('getAttribute')->with(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER)->andReturn($mockProvider2);
        $mockItem3->shouldReceive('getAttribute')->with(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER)->andReturn($mockProvider2);

        $mockProvider1->shouldReceive('getMapOfPerPageSortNamesToUntranslatedLabels')->andReturn(array('sortname' => 'provider1 sort'));
        $mockProvider2->shouldReceive('getMapOfPerPageSortNamesToUntranslatedLabels')->andReturn(array('sortname' => 'provider2 sort'));

        $mockProvider1->shouldReceive('getName')->andReturn('provider 1 name');
        $mockProvider2->shouldReceive('getName')->andReturn('provider 2 name');
        $mockProvider2->shouldReceive('getDisplayName')->andReturn('provider 2 friendly name');
        $mockProvider2->shouldReceive('compareForPerPageSort')->atLeast(1)->with(

            ehough_mockery_Mockery::type('tubepress_core_media_item_api_MediaItem'),
            ehough_mockery_Mockery::type('tubepress_core_media_item_api_MediaItem'),
            'sortname'
        )->andReturnUsing(function () {

           $array = array(0, 1, -1);
           $randKeys = array_rand($array, 1);
           return $randKeys[0];
        });


        $toReturn[] = $mockItem1;
        $toReturn[] = $mockItem2;
        $toReturn[] = $mockItem3;

        return $toReturn;
    }
}

