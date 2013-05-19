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
class tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_sut = new tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper();
    }

    public function testYouTubeFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setTotalResultCount(999);
        $providerResult->setVideos($videoArray);

        $event = new tubepress_spi_event_EventBase($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(50, $event->getSubject()->getTotalResultCount());
    }

}

