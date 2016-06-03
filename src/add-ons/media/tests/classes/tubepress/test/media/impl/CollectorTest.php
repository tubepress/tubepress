<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_media_impl_Collector<extended>
 */
class tubepress_test_media_impl_CollectorTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_media_impl_Collector
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockContext         = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEnvironment     = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_media_impl_Collector(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockEventDispatcher,
            $this->_mockEnvironment
        );
    }

    public function testGetPageNobodyHandled()
    {
        $this->setExpectedException('RuntimeException', 'No media providers were able to fulfill the page request for <code>some source</code>');

        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(false);

        $this->_mockContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array(
            'foo'                                       => 'bar',
            tubepress_api_options_Names::GALLERY_SOURCE => 'some source',
        ));
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');

        $mockCollectionEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(false);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78,
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $this->_sut->collectPage(78);
    }

    public function testGetPageExplicitMode()
    {
        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);
        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(false);

        $this->_mockContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array(
            'foo'                                       => 'bar',
            tubepress_api_options_Names::GALLERY_SOURCE => 'some source',
        ));
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');

        $mockMediaPage = $this->mock('tubepress_api_media_MediaPage');

        $mockCollectionEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(true);
        $mockCollectionEvent->shouldReceive('getArgument')->once()->with('mediaPage')->andReturn($mockMediaPage);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78,
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $result = $this->_sut->collectPage(78);

        $this->assertSame($mockMediaPage, $result);
    }

    public function testGetPageNoExplicitModeNoSources()
    {
        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);
        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(false);

        $this->_mockContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array(
            'foo' => 'bar',
        ));
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SOURCES)
            ->andReturn('');

        $mockMediaPage = $this->mock('tubepress_api_media_MediaPage');

        $mockCollectionEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(true);
        $mockCollectionEvent->shouldReceive('getArgument')->once()->with('mediaPage')->andReturn($mockMediaPage);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78,
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $result = $this->_sut->collectPage(78);

        $this->assertSame($mockMediaPage, $result);
    }

    public function testGetPageNoExplicitModeStoredSources()
    {
        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);
        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(false);

        $this->_mockContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array(
            'foo' => 'bar',
        ));
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE)
            ->andReturn('some source');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::SOURCES)
            ->andReturn(json_encode(array(array('stored' => 'source'))));
        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with(array(
           'foo'                                        => 'bar',
            'stored'                                    => 'source',
            tubepress_api_options_Names::GALLERY_SOURCE => 'some source',
        ));
        $this->_mockContext->shouldReceive('setEphemeralOptions')->once()->with(array(
            'foo' => 'bar',
        ));

        $mockMediaPage = $this->mock('tubepress_api_media_MediaPage');

        $mockCollectionEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockCollectionEvent->shouldReceive('hasArgument')->once()->with('mediaPage')->andReturn(true);
        $mockCollectionEvent->shouldReceive('getArgument')->once()->with('mediaPage')->andReturn($mockMediaPage);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('some source', array(
            'pageNumber' => 78,
        ))->andReturn($mockCollectionEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_PAGE_REQUEST, $mockCollectionEvent);

        $result = $this->_sut->collectPage(78);

        $this->assertSame($mockMediaPage, $result);
    }

    public function testGetSingle()
    {
        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);

        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('hasArgument')->once()->with('mediaItem')->andReturn(true);
        $mockEvent->shouldReceive('getArgument')->once()->with('mediaItem')->andReturn($mockMediaItem);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('xyz')->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_ITEM_REQUEST, $mockEvent);

        $result = $this->_sut->collectSingle('xyz');

        $this->assertSame($mockMediaItem, $result);
    }

    public function testGetSingleNobodyHandled()
    {
        $this->setExpectedException('RuntimeException', 'No acceptable providers for item');

        $this->_mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('hasArgument')->once()->with('mediaItem')->andReturn(false);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('xyz')->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_api_event_Events::MEDIA_ITEM_REQUEST, $mockEvent);

        $this->_sut->collectSingle('xyz');
    }
}
