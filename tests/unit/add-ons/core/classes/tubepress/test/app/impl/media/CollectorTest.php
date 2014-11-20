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
 * @covers tubepress_app_impl_media_Collector<extended>
 */
class tubepress_test_app_media_provider_impl_CollectorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_media_Collector
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockContext            = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockLogger                      = $this->mock(tubepress_platform_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_app_impl_media_Collector(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockEventDispatcher
        );
    }

    public function testGetPageNobodyHandled()
    {
        $this->setExpectedException('RuntimeException', 'No acceptable providers');

        $this->_mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');

        $mockMediaPage = $this->mock('tubepress_app_api_media_MediaPage');

        $mockCollectionEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(false);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $this->_sut->collectPage(78);
    }

    public function testGetPage()
    {
        $this->_mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');

        $mockMediaPage = $this->mock('tubepress_app_api_media_MediaPage');

        $mockCollectionEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(true);
        $mockCollectionEvent->shouldReceive('getArgument')->once()->with('mediaPage')->andReturn($mockMediaPage);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $result = $this->_sut->collectPage(78);

        $this->assertSame($mockMediaPage, $result);
    }

    public function testGetSingle()
    {
        $this->_mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);

        $mockMediaItem = $this->mock('tubepress_app_api_media_MediaItem');

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('hasArgument')->once()->with('mediaItem')->andReturn(true);
        $mockEvent->shouldReceive('getArgument')->once()->with('mediaItem')->andReturn($mockMediaItem);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('xyz')->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_api_event_Events::MEDIA_ITEM_REQUEST, $mockEvent);

        $result = $this->_sut->collectSingle('xyz');

        $this->assertSame($mockMediaItem, $result);
    }

    public function testGetSingleNobodyHandled()
    {
        $this->setExpectedException('RuntimeException', 'No acceptable providers for item');

        $this->_mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('hasArgument')->once()->with('mediaItem')->andReturn(false);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('xyz')->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_app_api_event_Events::MEDIA_ITEM_REQUEST, $mockEvent);

        $this->_sut->collectSingle('xyz');
    }
}