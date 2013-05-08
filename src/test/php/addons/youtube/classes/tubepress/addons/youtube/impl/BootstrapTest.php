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
class tubepress_addons_youtube_impl_BootstrapTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var tubepress_addons_youtube_impl_Bootstrap
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_addons_youtube_impl_Bootstrap();
    }

    public function testLoad()
    {
        $this->_testEventListenerRegistration();

        $this->_sut->boot();

        $this->assertTrue(true);
    }

    private function _testEventListenerRegistration()
    {
        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_youtube_impl_listeners_boot_YouTubeOptionsRegistrar', 'onBoot')
        );

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::VIDEO_CONSTRUCTION,
            array('tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
            'onVideoConstruction')
        );

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            ehough_shortstop_api_Events::RESPONSE,
            array('tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener',
            'onResponse')
        );

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET,
            array('tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistPlPrefixRemover', 'onPreValidationOptionSet')
        );
    }
}