<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_feed_impl_listeners_AcceptableValuesListener
 */
class tubepress_test_feed_impl_listeners_AcceptableValuesListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_feed_impl_listeners_AcceptableValuesListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIncomingEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider2;

    public function onSetup()
    {
        $this->_sut = new tubepress_feed_impl_listeners_AcceptableValuesListener();

        $this->_mockIncomingEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockMediaProvider1 = $this->mock(tubepress_spi_media_MediaProviderInterface::__);
        $this->_mockMediaProvider2 = $this->mock(tubepress_spi_media_MediaProviderInterface::__);

        $this->_sut->setMediaProviders(array(
            $this->_mockMediaProvider1,
            $this->_mockMediaProvider2
        ));
    }

    public function testMode()
    {
        $this->_mockMediaProvider1->shouldReceive('getGallerySourceNames')->once()->andReturn(array(
            'a', 'b'
        ));
        $this->_mockMediaProvider2->shouldReceive('getGallerySourceNames')->once()->andReturn(array(
            'c', 'd'
        ));

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array('foo', 'bar'));
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array(
            'foo', 'bar',
            'a', 'b', 'c', 'd',
        ));

        $this->_sut->onMode($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function testPerPageSort()
    {
        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array(
            'foo' => 'bar',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NONE          => 'none',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_COMMENT_COUNT => 'comment count',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NEWEST        => 'date published (newest first)',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_OLDEST        => 'date published (oldest first)',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_DURATION      => 'length',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM        => 'random',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_TITLE         => 'title',
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_VIEW_COUNT    => 'view count',
        ));

        $this->_sut->onPerPageSort($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }
}
