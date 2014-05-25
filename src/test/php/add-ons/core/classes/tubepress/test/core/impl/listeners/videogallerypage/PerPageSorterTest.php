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
 * @covers tubepress_core_impl_listeners_videogallerypage_PerPageSorter
 */
class tubepress_test_core_impl_listeners_videogallerypage_PerPageSorterTest extends tubepress_test_TubePressUnitTest
{
    /** @var tubepress_core_impl_listeners_videogallerypage_PerPageSorter */
    private $_sut;

    /**
     * @var array
     */
    private $_videos;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;


    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_sut = new tubepress_core_impl_listeners_videogallerypage_PerPageSorter($this->_mockLogger, $this->_mockExecutionContext);
        $this->_buildVideos();
    }

    public function testSortOrderNone()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_COMMENT_COUNT, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_NONE);

        $event = $this->mock('tubepress_core_api_event_EventInterface');

        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testSortBothRandom()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_RANDOM);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testCommentCount()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_COMMENT_COUNT);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testDuration()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_DURATION);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testNewest()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_NEWEST);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testOldest()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_OLDEST);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testRating()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_RATING);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testTitle()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_TITLE);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    public function testViewCount()
    {
        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM, tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_VIEW_COUNT);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->twice()->andReturn($providerResult);
        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

    private function _verifySort($videos, $first, $second, $third)
    {
        $this->assertTrue($videos[0]->xxx == $first, "First video should have been $first but was " . $videos[0]->xxx);
        $this->assertTrue($videos[1]->xxx == $second, "Second video should have been $second but was " . $videos[1]->xxx);
        $this->assertTrue($videos[2]->xxx == $third, "Third video should have been $third but was " . $videos[2]->xxx);
    }

    private function _setSortAndPerPageOrder($feed, $perPage)
    {
        $this->_mockExecutionContext->shouldReceive('get')->with(tubepress_core_api_const_options_Names::ORDER_BY)->andReturn($feed);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::PER_PAGE_SORT)->andReturn($perPage);
    }

    private function _buildVideos()
    {
        $v1 = new tubepress_core_api_video_Video();
        $v1->xxx = 1;
        $v1->setCommentCount(100);
        $v1->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 10);
        $v1->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 4000);
        $v1->setRatingAverage('1.5');
        $v1->setTitle('one');
        $v1->setViewCount(500);

        $v2 = new tubepress_core_api_video_Video();
        $v2->xxx = 2;
        $v2->setCommentCount(200);
        $v2->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 30);
        $v2->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 3000);
        $v2->setRatingAverage('3.5');
        $v2->setTitle('two');
        $v2->setViewCount(600);

        $v3 = new tubepress_core_api_video_Video();
        $v3->xxx = 3;
        $v3->setCommentCount(300);
        $v3->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 20);
        $v3->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 2000);
        $v3->setRatingAverage('2.5');
        $v3->setTitle('three');
        $v3->setViewCount(700);

        $this->_videos = array(

            $v1, $v2, $v3
        );

        shuffle($this->_videos);

    }
}

