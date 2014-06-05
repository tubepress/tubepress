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
 * @covers tubepress_core_media_provider_impl_listeners_page_ItemPrepender
 */
class tubepress_test_core_media_provider_impl_listeners_page_VideoPrependerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_provider_impl_listeners_page_ItemPrepender
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);

        $this->_mockVideoProvider = $this->mock(tubepress_core_media_provider_api_CollectorInterface::_);

        $this->_sut = new tubepress_core_media_provider_impl_listeners_page_ItemPrepender($this->_mockLogger, $this->_mockHttpRequestParameterService, $this->_mockVideoProvider);
    }

    public function testCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO)->andReturn('custom-video');

        $mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockMediaProvider->shouldReceive('getAttributeNameOfItemId')->atLeast(1)->andReturn('id');

        $video = new tubepress_core_media_item_api_MediaItem('video-id');
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockMediaProvider);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array($video));

        $this->_mockVideoProvider->shouldReceive('collectSingle')->once()->with('custom-video')->andReturn('x');

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->times(2)->andReturn($providerResult);

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array('x', $video), $providerResult->getItems());
    }

    public function testNoCustomVideo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_VIDEO)->andReturn('');

        $providerResult = new tubepress_core_media_provider_api_Page();

        $event = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_sut->onVideoGalleryPage($event);

        $this->assertEquals(array(), $providerResult->getItems());
    }
}