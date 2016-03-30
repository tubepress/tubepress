<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_listeners_media_HttpItemListener
 */
class tubepress_test_dailymotion_impl_listeners_media_HttpItemListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_listeners_media_HttpItemListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAttributeFormatter;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var tubepress_url_impl_puzzle_UrlFactory
     */
    private $_mockUrlFactory;

    /**
     * @var tubepress_array_impl_ArrayReader
     */
    private $_mockArrayReader;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockAttributeFormatter = $this->mock(tubepress_api_media_AttributeFormatterInterface::_);
        $this->_mockContext            = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockUrlFactory         = new tubepress_url_impl_puzzle_UrlFactory();
        $this->_mockArrayReader        = new tubepress_array_impl_ArrayReader();
        $this->_mockEvent              = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_dailymotion_impl_listeners_media_HttpItemListener(

            $this->_mockAttributeFormatter,
            $this->_mockContext,
            $this->_mockUrlFactory,
            $this->_mockArrayReader
        );
    }

    public function testHttpItem()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $mockMediaItem->shouldReceive('getId')->atLeast(1)->andReturn('media-item-id');

        $this->_mockContext->shouldReceive('get')->once()
            ->with(tubepress_dailymotion_api_Constants::OPTION_THUMB_SIZE)
            ->andReturn(tubepress_dailymotion_api_Constants::THUMB_SIZE_180);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($mockMediaItem);
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('feedAsArray')->andReturn(
            json_decode(file_get_contents(__DIR__ . '/../../../../../../../fixtures/feeds/simple.json'), true)
        );
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('zeroBasedIndex')->andReturn(2);

        $expectedRawAttributes = array(

            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME => 1454116245,
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID          => 'x1q0f7t',
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME     => 'Deuimad',
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL              => 'http://www.dailymotion.com/deuimad',
            tubepress_api_media_MediaItem::ATTRIBUTE_TITLE                   => 'Cognitive Psychology  Free Books',
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY           => array('Cognitive Psychology'),
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION             => 'Download Here http://readsbooksonline.com.playsterpdf.com/?book=0534514219<br /><br /><br /><br />Cognitive Psychology  Free Books <br />',
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS => 6,
            tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL         => 'http://www.dailymotion.com/video/x3pbbgh_cognitive-psychology-free-books_lifestyle',
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT       => 45363234,
            tubepress_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME => 'Fake Channel',
            tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL => 'http://s2.dmcdn.net/SsU0I/x180-Xzy.jpg',
        );

        foreach ($expectedRawAttributes as $attributeName => $attributeValue) {

            $mockMediaItem->shouldReceive('setAttribute')->once()->with($attributeName, $attributeValue);
        }

        $formatAttributesMap = array(

            'formatNumberAttribute' => array(


                array(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
                    tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED, 0),

            ),

            'truncateStringAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
                    tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
                    tubepress_api_options_Names::META_DESC_LIMIT),
            ),

            'formatDurationAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS,
                    tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED),
            ),

            'formatDateAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,
                    tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED),
            ),

            'implodeArrayAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY,
                    tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', '),
            )
        );

        foreach ($formatAttributesMap as $method => $calls) {

            foreach ($calls as $call) {

                if (count($call) > 2) {

                    $this->_mockAttributeFormatter->shouldReceive($method)->once()->with(
                        $mockMediaItem,
                        $call[0], $call[1], $call[2]
                    );

                } else {

                    $this->_mockAttributeFormatter->shouldReceive($method)->once()->with(
                        $mockMediaItem,
                        $call[0], $call[1]
                    );
                }
            }
        }

        $this->_sut->onHttpItem($this->_mockEvent);
    }
}