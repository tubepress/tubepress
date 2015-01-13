<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo2_impl_listeners_media_HttpItemListener
 */
class tubepress_test_vimeo2_impl_listeners_media_HttpItemListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo2_impl_listeners_media_HttpItemListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAttributeFormatter;

    public function onSetup()
    {
        $this->_mockExecutionContext   = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockAttributeFormatter = $this->mock(tubepress_app_api_media_AttributeFormatterInterface::_);

        $this->_sut = new tubepress_vimeo2_impl_listeners_media_HttpItemListener(

            $this->_mockAttributeFormatter,
            $this->_mockExecutionContext
        );
    }

    public function testConstructionGalleryXmlRandomThumbRelativeDates()
    {
        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onHttpItem($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_app_api_media_MediaItem);
        $this->assertEquals('Nunca Silva', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('nuncasilva', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('The assumption that most hobby fishermen are individuals of limited fashion sense is groundless, demonstrated by this man who has colour-coordinated his shirt and his hat with the light blue background of the lake.', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('61', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('http://vimeo.com/49078748', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL));
        $this->assertEquals('http://b.vimeocdn.com/ts/338/576/338576320_100.jpg', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1347129000', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('lone fisherman, colour coordinated', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE));
        $this->assertEquals('96321', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals('6', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT));
    }

    public function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/vimeo_v2/resources/vimeo-single-video.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str );

        return $out;
    }

    public function galleryXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/vimeo_v2/resources/vimeo-gallery.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array(get_class($this), '_callbackStrlen'), $serial_str );

        return $out;
    }

    private function _prepareEvent($feed, $index)
    {
        $item = new tubepress_app_api_media_MediaItem('id');
        $mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->andReturn('vimeo');
        $item->setAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockProvider);

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

        $event = new tubepress_lib_impl_event_tickertape_EventBase($item);
        $event->setArgument('unserializedFeed', $unserialized);
        $event->setArgument('videoArray', $videoArray);
        $event->setArgument('zeroBasedIndex', $index);

        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, 0);
        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);
        $this->_mockAttributeFormatter->shouldReceive('truncateStringAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_options_Names::META_DESC_LIMIT);
        $this->_mockAttributeFormatter->shouldReceive('formatDurationAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS, tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);
        $this->_mockAttributeFormatter->shouldReceive('formatDateAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);
        $this->_mockAttributeFormatter->shouldReceive('implodeArrayAttribute')->once()->with($item,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');
        
        return $event;
    }

    public function _callbackStrlen($matches)
    {
        $toReturn = "s:" . strlen($matches[2]) . ":\"" . $matches[2] . "\";";

        return $toReturn;
    }
}
