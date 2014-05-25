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
 * @covers tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener
 */
class tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTimeUtils;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockTimeUtils        = $this->mock(tubepress_core_api_util_TimeUtilsInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::DESC_LIMIT)->andReturn(9);
        $this->_sut = new tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener(

            $this->_mockExecutionContext,
            $this->_mockTimeUtils
        );
    }

    public function testConstructionGalleryXmlStaticThumbAbsoluteDates()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::RELATIVE_DATES)->andReturn(false);

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_mockTimeUtils->shouldReceive('secondsToHumanTime')->once()->with('61')->andReturn('human time');
        $this->_mockTimeUtils->shouldReceive('unixTimeToHumanReadable')->once()->with(1347129000, false)->andReturn('human readable');

        $this->_sut->onVideoConstruction($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_core_api_video_Video);
        $this->assertEquals('vimeo', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME));
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assum...', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('human time', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_FORMATTED));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_HOME_URL));
        $this->assertEquals('49078748', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_ID));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('human readable', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_FORMATTED));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TITLE));
        $this->assertEquals('96,321', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_LIKES_COUNT));
    }

    public function testConstructionGalleryXmlRandomThumbRelativeDates()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::RELATIVE_DATES)->andReturn(true);

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_mockTimeUtils->shouldReceive('secondsToHumanTime')->once()->with('61')->andReturn('human time');
        $this->_mockTimeUtils->shouldReceive('unixTimeToHumanReadable')->once()->with(1347129000, true)->andReturn('human readable');

        $this->_sut->onVideoConstruction($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_core_api_video_Video);
        $this->assertEquals('vimeo', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME));
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assum...', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('human time', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_DURATION_FORMATTED));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_HOME_URL));
        $this->assertEquals('49078748', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_ID));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('human readable', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_FORMATTED));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_TITLE));
        $this->assertEquals('96,321', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_core_api_video_Video::ATTRIBUTE_LIKES_COUNT));
    }

    public function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/vimeo/vimeo-single-video.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/vimeo/vimeo-gallery.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    private function _prepareEvent($feed, $index)
    {
        $video = new tubepress_core_api_video_Video();
        $video->setAttribute(tubepress_core_api_video_Video::ATTRIBUTE_PROVIDER_NAME, 'vimeo');

        $unserialized = @unserialize($feed);
        $videoArray   = array();

        /*
         * Is this just a single video?
         */
        if (isset($unserialized->video)) {

            $videoArray = (array) $unserialized->video;

        } else if (isset($unserialized->videos) && isset($unserialized->videos->video)) {

            $videoArray = (array) $unserialized->videos->video;
        }

        $event = new tubepress_core_impl_event_tickertape_EventBase($video);
        $event->setArgument('unserializedFeed', $unserialized);
        $event->setArgument('videoArray', $videoArray);
        $event->setArgument('rawFeed', $feed);
        $event->setArgument('zeroBasedFeedIndex', $index);

        return $event;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
