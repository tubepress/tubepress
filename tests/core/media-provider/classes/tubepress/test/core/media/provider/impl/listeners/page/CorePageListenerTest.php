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
 * @covers tubepress_core_media_provider_impl_listeners_page_CorePageListener
 */
class tubepress_test_core_media_provider_impl_listeners_page_CorePageListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_provider_impl_listeners_page_CorePageListener
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
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockPage             = $this->mock('tubepress_core_media_provider_api_Page');
        $this->_mockItemArray        = $this->_buildMockItemArray();
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockCollector       = $this->mock(tubepress_core_media_provider_api_CollectorInterface::_);


        $this->_mockPage->shouldReceive('getItems')->andReturn($this->_mockItemArray);
        $this->_mockEvent->shouldReceive('getSubject')->andReturn($this->_mockPage);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_sut = new tubepress_core_media_provider_impl_listeners_page_CorePageListener(
            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockHttpRequestParameterService,
            $this->_mockCollector
        );
    }

    public function testPrependCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO)->andReturn('custom-video');

        $mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockMediaProvider->shouldReceive('getAttributeNameOfItemId')->atLeast(1)->andReturn('id');

        $video = new tubepress_core_media_item_api_MediaItem('video-id');
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockMediaProvider);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array($video));

        $this->_mockCollector->shouldReceive('collectSingle')->once()->with('custom-video')->andReturn('x');

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->prependItems($event);

        $this->assertEquals(array('x', $video), $providerResult->getItems());
    }

    public function testPrependNoCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO)->andReturn('');

        $providerResult = new tubepress_core_media_provider_api_Page();

        $event = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_sut->prependItems($event);

        $this->assertEquals(array(), $providerResult->getItems());
    }

    public function testBlacklist()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_ITEM_ID_BLACKLIST)->andReturn('xxx');

        $mockVideoProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockVideoProvider->shouldReceive('getAttributeNameOfItemId')->atLeast(1)->andReturn('id');

        $mockVideo1 = new tubepress_core_media_item_api_MediaItem('p');
        $mockVideo1->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo2 = new tubepress_core_media_item_api_MediaItem('y');
        $mockVideo2->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo3 = new tubepress_core_media_item_api_MediaItem('xxx');
        $mockVideo3->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $mockVideo4 = new tubepress_core_media_item_api_MediaItem('yyy');
        $mockVideo4->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockVideoProvider);

        $videoArray = array($mockVideo1, $mockVideo2, $mockVideo3, $mockVideo4);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems($videoArray);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->blacklist($event);

        $this->assertEquals(array($mockVideo1, $mockVideo2, $mockVideo4), $providerResult->getItems());
    }

    public function testCapResults()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_RESULT_COUNT_CAP)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn(tubepress_youtube_api_Constants::GALLERYSOURCE_YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setTotalResultCount(999);
        $providerResult->setItems($videoArray);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->times(3)->andReturn($providerResult);

        $this->_sut->capResults($event);

        $this->assertTrue(true);
    }

    public function testPerPageProviderSort()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, 'sortname');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest('perPageSort');
    }

    public function testPerPageSortNobodyCanHandle()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, 'x');

        $this->_mockPage->shouldReceive('setItems')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return is_array($arg);
        }));

        $this->_runTest('perPageSort');
    }

    public function testPerPageSortOrderNone()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_NONE);

        $this->_runTest('perPageSort');
    }

    public function testPerPageRandom()
    {
        $this->_setSortAndPerPageOrder(tubepress_core_media_provider_api_Constants::ORDER_BY_DEFAULT, tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_RANDOM);

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

