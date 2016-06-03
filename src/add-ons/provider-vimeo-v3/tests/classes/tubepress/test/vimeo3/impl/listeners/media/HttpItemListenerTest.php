<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo3_impl_listeners_media_HttpItemListener
 */
class tubepress_test_vimeo3_impl_listeners_media_HttpItemListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo3_impl_listeners_media_HttpItemListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAttributeFormatter;

    public function onSetup()
    {
        $this->_mockExecutionContext   = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockAttributeFormatter = $this->mock(tubepress_api_media_AttributeFormatterInterface::_);

        $this->_sut = new tubepress_vimeo3_impl_listeners_media_HttpItemListener(

            $this->_mockAttributeFormatter,
            $this->_mockExecutionContext
        );
    }

    public function testConstructionGalleryXmlRandomThumbRelativeDates()
    {
        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onHttpItem($event);

        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_api_media_MediaItem);
        $this->assertEquals('Guardian News & Media Ltd', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('13301326', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('When a person dies at the hands of law enforcement, they leave behind parents and children, loss and confusion. What’s to be done when those you’d call are the same people that pulled the trigger?

In 2015, these officer-involved shootings were more likely in Kern County, California, than anywhere in the United States. Meet the families there grieving, calling for justice. They protest split-second decisions made by officers and deputies who feel constantly threatened in a county that overwhelmingly supports law and order by any means.', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('800', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('https://vimeo.com/148662087', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL));
        $this->assertEquals('https://i.vimeocdn.com/video/547671085_100x75.jpg?r=pad', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('1449863882', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('The County', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_TITLE));
        $this->assertEquals('54', $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT));
        $this->assertEquals(null, $video->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT));
    }

    public function singleVideoXml()
    {
        $serial_str = file_get_contents(TUBEPRESS_ROOT . '/src/add-ons/provider-vimeo-v3/resources/vimeo-single-video.txt');

        $out = preg_replace_callback('!s:(\d+):"(.*?)";!s', array('tubepress_test_vimeo_impl_listeners_video_VimeoVideoConstructionListenerTest', '_callbackStrlen'), $serial_str);

        return $out;
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/add-ons/provider-vimeo-v3/tests/fixtures/video-list.json');
    }

    private function _prepareEvent($feed, $index)
    {
        $item         = new tubepress_api_media_MediaItem('id');
        $mockProvider = $this->mock(tubepress_spi_media_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->andReturn('vimeo');
        $item->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER, $mockProvider);

        $unserialized = json_decode($feed, true);
        $videoArray   = array();

        /*
         * Is this just a single video?
         */
        if (!isset($unserialized['data'])) {

            $videoArray = (array) $unserialized->video;

        } else {

            $videoArray = (array) $unserialized['data'];
        }

        $event = new tubepress_event_impl_tickertape_EventBase($item);
        $event->setArgument('decodedJson', $unserialized);
        $event->setArgument('videoArray', $videoArray);
        $event->setArgument('zeroBasedIndex', $index);

        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT, 0);
        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);
        $this->_mockAttributeFormatter->shouldReceive('truncateStringAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_api_options_Names::META_DESC_LIMIT);
        $this->_mockAttributeFormatter->shouldReceive('formatDurationAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS, tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);
        $this->_mockAttributeFormatter->shouldReceive('formatDateAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);
        $this->_mockAttributeFormatter->shouldReceive('implodeArrayAttribute')->once()->with($item,
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');

        return $event;
    }
}
