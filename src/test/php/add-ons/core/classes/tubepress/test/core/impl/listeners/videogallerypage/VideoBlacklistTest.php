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
 * @covers tubepress_core_impl_listeners_videogallerypage_VideoBlacklist
 */
class tubepress_test_core_impl_listeners_videogallerypage_VideoBlacklistTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_videogallerypage_VideoBlacklist
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


        $this->_sut = new tubepress_core_impl_listeners_videogallerypage_VideoBlacklist($this->_mockLogger, $this->_mockExecutionContext);
    }

    public function testYouTubeFavorites()
    {
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::VIDEO_BLACKLIST)->andReturn('xxx');

        $mockVideo1 = new tubepress_core_api_video_Video();
        $mockVideo1->setId('p');

        $mockVideo2 = new tubepress_core_api_video_Video();
        $mockVideo2->setId('y');

        $mockVideo3 = new tubepress_core_api_video_Video();
        $mockVideo3->setId('xxx');

        $mockVideo4 = new tubepress_core_api_video_Video();
        $mockVideo4->setId('yyy');

        $videoArray = array($mockVideo1, $mockVideo2, $mockVideo3, $mockVideo4);

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos($videoArray);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array($mockVideo1, $mockVideo2, $mockVideo4), $providerResult->getVideos());
    }

}

