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
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockTimeUtils        = $this->mock(tubepress_core_util_api_TimeUtilsInterface::_);

        $this->_sut = new tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener(

            $this->_mockExecutionContext,
            $this->_mockTimeUtils
        );
    }

    public function testConstructionGalleryXmlRandomThumbRelativeDates()
    {
        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onVideoConstruction($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_core_media_item_api_MediaItem);
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assumption that most hobby fishermen are individuals of limited fashion sense is groundless, demonstrated by this man who has colour-coordinated his shirt and his hat with the light blue background of the lake.', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE));
        $this->assertEquals('96321', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_LIKES_COUNT));
    }

    public function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/add-ons/vimeo/resources/vimeo/vimeo-single-video.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/test/add-ons/vimeo/resources/vimeo/vimeo-gallery.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    private function _prepareEvent($feed, $index)
    {
        $video = new tubepress_core_media_item_api_MediaItem('id');
        $mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->andReturn('vimeo');
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockProvider);

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

        $event = new tubepress_core_event_impl_tickertape_EventBase($video);
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
