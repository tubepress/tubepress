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
 * @covers tubepress_app_impl_listeners_media_PageListener
 */
class tubepress_test_app_impl_listeners_media_PageListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_media_PageListener
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCollector;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockPage             = $this->mock('tubepress_app_api_media_MediaPage');
        $this->_mockItemArray        = $this->_buildMockItemArray();
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockCollector       = $this->mock(tubepress_app_api_media_CollectorInterface::_);


        $this->_mockPage->shouldReceive('getItems')->andReturn($this->_mockItemArray);
        $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->_mockPage);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_sut = new tubepress_app_impl_listeners_media_PageListener(
            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockHttpRequestParameterService,
            $this->_mockCollector
        );
    }

    public function testPrependCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('custom-video');

        $mockMediaProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);
        $mockMediaProvider->shouldReceive('getAttributeNameOfItemId')->atLeast(1)->andReturn('id');

        $video = new tubepress_app_api_media_MediaItem('video-id');
        $video->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockMediaProvider);

        $providerResult = new tubepress_app_api_media_MediaPage();
        $providerResult->setItems(array($video));

        $this->_mockCollector->shouldReceive('collectSingle')->once()->with('custom-video')->andReturn('x');

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->prependItems($event);

        $this->assertEquals(array('x', $video), $providerResult->getItems());
    }

    public function testPrependNoCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubepress_item')->andReturn('');

        $providerResult = new tubepress_app_api_media_MediaPage();

        $event = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_sut->prependItems($event);

        $this->assertEquals(array(), $providerResult->getItems());
    }

    public function testBlacklist()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST)->andReturn('xxx');

        $mockVideoProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);

        $mockVideo1 = new tubepress_app_api_media_MediaItem('p');
        $mockVideo1->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo2 = new tubepress_app_api_media_MediaItem('y');
        $mockVideo2->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo3 = new tubepress_app_api_media_MediaItem('xxx');
        $mockVideo3->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo4 = new tubepress_app_api_media_MediaItem('yyy');
        $mockVideo4->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $videoArray = array($mockVideo1, $mockVideo2, $mockVideo3, $mockVideo4);

        $providerResult = new tubepress_app_api_media_MediaPage();
        $providerResult->setItems($videoArray);

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->blacklist($event);

        $this->assertEquals(array($mockVideo1, $mockVideo2, $mockVideo4), $providerResult->getItems());
    }

    public function testCapResults()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn(tubepress_youtube2_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = new tubepress_app_api_media_MediaPage();
        $providerResult->setTotalResultCount(999);
        $providerResult->setItems($videoArray);

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(3)->andReturn($providerResult);

        $this->_sut->capResults($event);

        $this->assertTrue(true);
    }

    public function testPerPageProviderSort()
    {
        $this->_setSortAndPerPageOrder(tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT, 'sortname');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest('perPageSort');
    }

    public function testPerPageSortNobodyCanHandle()
    {
        $this->_setSortAndPerPageOrder(tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT, 'x');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest('perPageSort');
    }

    public function testPerPageSortOrderNone()
    {
        $this->_setSortAndPerPageOrder(tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT, tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE);

        $this->_runTest('perPageSort');
    }

    public function testPerPageRandom()
    {
        $this->_setSortAndPerPageOrder(tubepress_app_api_options_AcceptableValues::ORDER_BY_DEFAULT, tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM);

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest('perPageSort');
    }

    private function _runTest($method)
    {
        $this->_sut->$method($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _setSortAndPerPageOrder($feed, $perPage)
    {
        $this->_mockExecutionContext->shouldReceive('get')->with(tubepress_app_api_options_Names::FEED_ORDER_BY)->andReturn($feed);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::FEED_PER_PAGE_SORT)->andReturn($perPage);
    }

    private function _buildMockItemArray()
    {
        $toReturn = array();

        $mockItem1 = $this->mock('tubepress_app_api_media_MediaItem');
        $mockItem2 = $this->mock('tubepress_app_api_media_MediaItem');
        $mockItem3 = $this->mock('tubepress_app_api_media_MediaItem');

        $mockProvider1 = $this->mock('tubepress_app_api_media_MediaProviderInterface');
        $mockProvider2 = $this->mock('tubepress_app_api_media_MediaProviderInterface');

        $mockItem1->shouldReceive('getAttribute')->with(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER)->andReturn($mockProvider1);
        $mockItem2->shouldReceive('getAttribute')->with(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER)->andReturn($mockProvider2);
        $mockItem3->shouldReceive('getAttribute')->with(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER)->andReturn($mockProvider2);

        $mockProvider1->shouldReceive('getMapOfPerPageSortNamesToUntranslatedLabels')->andReturn(array('sortname' => 'provider1 sort'));
        $mockProvider2->shouldReceive('getMapOfPerPageSortNamesToUntranslatedLabels')->andReturn(array('sortname' => 'provider2 sort'));

        $mockProvider1->shouldReceive('getName')->andReturn('provider 1 name');
        $mockProvider2->shouldReceive('getName')->andReturn('provider 2 name');
        $mockProvider2->shouldReceive('getDisplayName')->andReturn('provider 2 friendly name');
        $mockProvider2->shouldReceive('compareForPerPageSort')->atLeast(1)->with(

            ehough_mockery_Mockery::type('tubepress_app_api_media_MediaItem'),
            ehough_mockery_Mockery::type('tubepress_app_api_media_MediaItem'),
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

