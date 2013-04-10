<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorterTest extends TubePressUnitTest
{
    /** @var tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter */
    private $_sut;

    /**
     * @var array
     */
    private $_videos;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_sut = new tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter();
        $this->_buildVideos();
    }

    public function testSortOrderNone()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT, tubepress_api_const_options_values_PerPageSortValue::NONE);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals($providerResult, $event->getSubject());
    }

    public function testSortBothRandom()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::RANDOM);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals($providerResult, $event->getSubject());
    }

    public function testCommentCount()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 3, 2, 1);
    }

    public function testDuration()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::DURATION);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 2, 3, 1);
    }

    public function testNewest()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::NEWEST);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 1, 2, 3);
    }

    public function testOldest()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::OLDEST);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 3, 2, 1);
    }

    public function testRating()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::RATING);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 2, 3, 1);
    }

    public function testTitle()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::TITLE);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 1, 3, 2);
    }

    public function testViewCount()
    {
        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT);

        $event = new tubepress_api_event_TubePressEvent($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->_verifySort($event->getSubject()->getVideos(), 3,2,1);
    }

    private function _verifySort($videos, $first, $second, $third)
    {
        $this->assertTrue($videos[0]->xxx == $first, "First video should have been $first but was " . $videos[0]->xxx);
        $this->assertTrue($videos[1]->xxx == $second, "Second video should have been $second but was " . $videos[1]->xxx);
        $this->assertTrue($videos[2]->xxx == $third, "Third video should have been $third but was " . $videos[2]->xxx);
    }

    private function _setSortAndPerPageOrder($feed, $perPage)
    {


        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn($feed);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Feed::PER_PAGE_SORT)->andReturn($perPage);
    }

    private function _buildVideos()
    {
        $v1 = new tubepress_api_video_Video();
        $v1->xxx = 1;
        $v1->setCommentCount(100);
        $v1->setAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 10);
        $v1->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 4000);
        $v1->setRatingAverage('1.5');
        $v1->setTitle('one');
        $v1->setViewCount(500);

        $v2 = new tubepress_api_video_Video();
        $v2->xxx = 2;
        $v2->setCommentCount(200);
        $v2->setAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 30);
        $v2->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 3000);
        $v2->setRatingAverage('3.5');
        $v2->setTitle('two');
        $v2->setViewCount(600);

        $v3 = new tubepress_api_video_Video();
        $v3->xxx = 3;
        $v3->setCommentCount(300);
        $v3->setAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS, 20);
        $v3->setAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, 2000);
        $v3->setRatingAverage('2.5');
        $v3->setTitle('three');
        $v3->setViewCount(700);

        $this->_videos = array(

            $v1, $v2, $v3
        );

        shuffle($this->_videos);

    }
}

