<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_plugins_core_filters_videogallerypage_PerPageSorterTest extends TubePressUnitTest
{
	private $_sut;

	private $_videos;

    private $_mockExecutionContext;

	function setup()
	{
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

		$this->_sut = new tubepress_plugins_core_filters_videogallerypage_PerPageSorter();
		$this->_buildVideos();
	}

	function testSortOrderNone()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();

        $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT, tubepress_api_const_options_values_PerPageSortValue::NONE);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals($providerResult, $event->getSubject());
	}

	function testSortBothRandom()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::RANDOM);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

	    $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals($providerResult, $event->getSubject());
	}

	function testCommentCount()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

	    $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 3, 2, 1);
	}

	function testDuration()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::DURATION);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

	    $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 2, 3, 1);
	}

	function testNewest()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::NEWEST);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

	    $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 1, 2, 3);
	}

	function testOldest()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::OLDEST);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

	    $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 3, 2, 1);
	}

	function testRating()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::RATING);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

        $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 2, 3, 1);
	}

	function testTitle()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::TITLE);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

        $this->_sut->onVideoGalleryPage($event);

	    $this->_verifySort($event->getSubject()->getVideos(), 1, 3, 2);
	}

	function testViewCount()
	{
	    $providerResult = new tubepress_api_video_VideoGalleryPage();
	    $providerResult->setVideos($this->_videos);

	    $this->_setSortAndPerPageOrder(tubepress_api_const_options_values_OrderByValue::RANDOM, tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT);

        $event = new tubepress_api_event_VideoGalleryPageConstruction($providerResult);

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

