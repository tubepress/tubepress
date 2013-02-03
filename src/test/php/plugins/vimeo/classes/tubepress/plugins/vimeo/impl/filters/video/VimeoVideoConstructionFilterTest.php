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
class tubepress_plugins_vimeo_impl_filters_video_VimeoVideoConstructionFilterTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_youtube_impl_filters_video_YouTubeVideoConstructionFilter
     */
    private $_sut;

    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_vimeo_impl_filters_video_VimeoVideoConstructionFilter();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(9);
    }

    public function testConstructionGalleryXmlStaticThumbAbsoluteDates()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('l jS \of F Y h:i:s A');

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onVideoConstruction($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_api_video_Video);
        $this->assertEquals('vimeo', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME));
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assum...', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('1:01', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_FORMATTED));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_HOME_URL));
        $this->assertEquals('49078748', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_ID));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('Saturday 8th of September 2012 02:30:00 PM', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_FORMATTED));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TITLE));
        $this->assertEquals('96,321', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_LIKES_COUNT));
    }

    public function testConstructionGalleryXmlRandomThumbRelativeDates()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(true);

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onVideoConstruction($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_api_video_Video);
        $this->assertEquals('vimeo', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME));
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assum...', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('1:01', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_DURATION_FORMATTED));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_HOME_URL));
        $this->assertEquals('49078748', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_ID));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('5 months ago', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TIME_PUBLISHED_FORMATTED));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_TITLE));
        $this->assertEquals('96,321', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_LIKES_COUNT));
    }

    function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-single-video.txt');

        $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );

        return $out;
    }

    function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/feeds/vimeo-gallery.txt');

        $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );

        return $out;
    }

    private function _prepareEvent($feed, $index)
    {
        $video = new tubepress_api_video_Video();
        $video->setAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME, 'vimeo');

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

        $event = new tubepress_api_event_TubePressEvent($video);
        $event->setArgument('unserializedFeed', $unserialized);
        $event->setArgument('videoArray', $videoArray);
        $event->setArgument('rawFeed', $feed);
        $event->setArgument('zeroBasedFeedIndex', $index);

        return $event;
    }
}
