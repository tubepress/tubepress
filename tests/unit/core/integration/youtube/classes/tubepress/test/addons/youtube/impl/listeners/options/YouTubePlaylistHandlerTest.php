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
 * @covers tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler
 */
class tubepress_test_youtube_impl_listeners_options_YouTubePlaylistHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockUrlFactory = $this->mock(tubepress_lib_url_api_UrlFactoryInterface::_);
        $this->_mockStringUtils  = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);
        $this->_mockUrlFactory->shouldReceive('fromString')->atLeast(1)->andReturnUsing(function ($a) {

            $urlFactory = new tubepress_lib_url_impl_puzzle_UrlFactory($_SERVER);
            return $urlFactory->fromString($a);
        });
        $this->_mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut = new tubepress_youtube_impl_listeners_options_YouTubePlaylistHandler($this->_mockUrlFactory, $this->_mockStringUtils);
    }

    public function testPullListFromUrl()
    {
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('youtube.com', 'youtube.com')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('PL123', 'PL')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('replaceFirst')->once()->with('PL', '', 'PL123')->andReturn('123');

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('http://youtube.com/?list=PL123');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', '123');
        $this->_sut->onPreValidationOptionSet($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testPullListFromUrlNoListParam()
    {
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('youtube.com', 'youtube.com')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('http://youtube.com/?lt=123', 'PL')->andReturn(false);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('http://youtube.com/?lt=123');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'http://youtube.com/?lt=123');
        $this->_sut->onPreValidationOptionSet($this->_mockEvent);

        $this->assertTrue(true);

    }

    public function testPullListFromUrlNonYouTube()
    {
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('vimeo.com', 'youtube.com')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('http://vimeo.com/?list=123', 'PL')->andReturn(false);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('http://vimeo.com/?list=123');
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'http://vimeo.com/?list=123');

        $this->_sut->onPreValidationOptionSet($this->_mockEvent);

        $this->assertTrue(true);

    }

    public function testAlterNonString()
    {
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with(array('hello'), 'PL')->andReturn(false);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn(array('hello'));
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', array('hello'));

        $this->_sut->onPreValidationOptionSet($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testAlterHtmlNonPrefix()
    {
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('', 'youtube.com')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('hello', 'PL')->andReturn(false);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('hello');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'hello');
        $this->_sut->onPreValidationOptionSet($this->_mockEvent);

        $this->assertTrue(true);

    }

    public function testAlterPrefix()
    {
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('', 'youtube.com')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('PLhelloPL', 'PL')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('replaceFirst')->once()->with('PL', '', 'PLhelloPL')->andReturn('helloPL');

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn('PLhelloPL');

        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', 'helloPL');
        $this->_sut->onPreValidationOptionSet($this->_mockEvent);

        $this->assertTrue(true);

    }
}