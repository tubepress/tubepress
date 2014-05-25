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
 * @covers tubepress_core_impl_listeners_videogallerypage_ResultCountCapper
 */
class tubepress_test_core_impl_listeners_videogallerypage_ResultCountCapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_videogallerypage_ResultCountCapper
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

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_sut = new tubepress_core_impl_listeners_videogallerypage_ResultCountCapper(

            $this->_mockLogger,
            $this->_mockExecutionContext);
    }

    public function testYouTubeFavorites()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::RESULT_COUNT_CAP)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::GALLERY_SOURCE)->andReturn(tubepress_youtube_api_const_options_Values::YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setTotalResultCount(999);
        $providerResult->setVideos($videoArray);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(3)->andReturn($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertTrue(true);
    }

}

