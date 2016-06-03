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
 * @covers tubepress_youtube3_impl_listeners_media_HttpItemListener
 */
class tubepress_test_youtube3_impl_listeners_media_HttpItemListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube3_impl_listeners_media_HttpItemListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAttributeFormatter;

    /**
     * @var tubepress_util_impl_TimeUtils
     */
    private $_mockTimeUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockApiUtility;

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
        $this->_mockTimeUtils          = new tubepress_util_impl_TimeUtils(new tubepress_util_impl_StringUtils());
        $this->_mockContext            = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockApiUtility         = $this->mock('tubepress_youtube3_impl_ApiUtility');
        $this->_mockUrlFactory         = new tubepress_url_impl_puzzle_UrlFactory();
        $this->_mockArrayReader        = new tubepress_array_impl_ArrayReader();
        $this->_mockEvent              = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_youtube3_impl_listeners_media_HttpItemListener(

            $this->_mockAttributeFormatter,
            $this->_mockTimeUtils,
            $this->_mockContext,
            $this->_mockApiUtility,
            $this->_mockUrlFactory,
            $this->_mockArrayReader
        );
    }

    public function testHttpItem()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $mockMediaItem->shouldReceive('getId')->atLeast(1)->andReturn('media-item-id');

        $this->_mockEvent->shouldReceive('getSubject')->twice()->andReturn($mockMediaItem);
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('metadataAsArray')->andReturn(
            json_decode(file_get_contents(__DIR__ . '/../../../../../../../../resources/feeds/metadata.json'), true)
        );
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('zeroBasedIndex')->andReturn(1);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::GALLERY_RANDOM_THUMBS)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::META_DISPLAY_CATEGORY)->andReturn(false);

        $expectedRawAttributes = array(

            tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME => 1451768401,
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID          => 'UC1yBKRuGpC1tSM73A0ZjYjQ',
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME     => 'The Young Turks',
            tubepress_api_media_MediaItem::ATTRIBUTE_AUTHOR_URL              => 'https://www.youtube.com/channel/UC1yBKRuGpC1tSM73A0ZjYjQ',
            tubepress_api_media_MediaItem::ATTRIBUTE_TITLE                   => 'GOP Rep: Giving Trump An Army Is A Bad Idea',
            tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION             => 'Congressman Chris Gibson recently said he was concerned with Donald Trump controlling an army. Given some…err… all of the things Trump has said, this seems to be a valid concern. Cenk Uygur, host of the The Young Turks, breaks it down. Tell us what you think in the comment section below.<br />
<br />
"GOP Congressman Chris Gibson — a decorated veteran who served 29 years in the US Army — told radio host Bill Samuels Sunday that he has “concerns” about making Republican candidate Donald Trump Commander-in-Chief.<br />
<br />
“Look, I mean, he is running for President of the United States under this banner, the party that I’m in,” Gibson said on Effective Radio with Bill Samuels. “I have told people before I’m prayerful that you will see some kind of evolution in this guy.””*<br />
<br />
Read more here: http://www.mediaite.com/online/gop-rep-on-trump-i-have-concerns-about-giving-that-guy-an-army/<br />
***<br />
<br />
Get The Young Turks Mobile App Today!<br />
<br />
Download the iOS version here: https://itunes.apple.com/us/app/the-young-turks/id412793195?ls=1&mt=8<br />
<br />
Download the Android version here: https://play.google.com/store/apps/details?id=com.tyt',
            tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY => array(
                'TYT',
                'The Young Turks',
                'Cenk Uygur',
                'News',
                'Liberal',
                'Progressive',
                'Politics',
                'TYT Network',
                'Chris Gibson',
                'Donald Trump',
                'Republican',
                'Primary',
                '2016',
                'Army',
                'Bad Idea',
                'Control', ),
            tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS => 233,
            tubepress_api_media_MediaItem::ATTRIBUTE_HOME_URL         => 'https://youtu.be/media-item-id',
            tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT       => 75490,
            tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT      => 1795,
            tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES   => 225,
            tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED  => 0,
            tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT    => 704,
        );

        foreach ($expectedRawAttributes as $attributeName => $attributeValue) {

            $mockMediaItem->shouldReceive('setAttribute')->once()->with($attributeName, $attributeValue);
        }

        $mockMediaItem->shouldReceive('setAttribute')->once()->with(
            tubepress_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL,
            Mockery::on(function ($url) {

                return preg_match_all('~^http://i.ytimg.com/vi/NpOd4oLEKyg/[1,2,3].jpg$~', $url, $matches) === 1;
            })
        );

        $formatAttributesMap = array(

            'formatNumberAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED,
                    tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_FAVORITED_FORMATTED, 0, ),

                array(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
                    tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT_FORMATTED, 0, ),

                array(tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT,
                    tubepress_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0, ),

                array(tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT,
                    tubepress_api_media_MediaItem::ATTRIBUTE_COMMENT_COUNT_FORMATTED, 0, ),

                array(tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT,
                    tubepress_api_media_MediaItem::ATTRIBUTE_LIKES_COUNT_FORMATTED, 0, ),

                array(tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES,
                    tubepress_api_media_MediaItem::ATTRIBUTE_COUNT_DISLIKES_FORMATTED, 0, ),
            ),

            'truncateStringAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
                    tubepress_api_media_MediaItem::ATTRIBUTE_DESCRIPTION,
                    tubepress_api_options_Names::META_DESC_LIMIT, ),
            ),

            'formatDurationAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS,
                    tubepress_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED, ),
            ),

            'formatDateAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME,
                    tubepress_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED, ),
            ),

            'implodeArrayAttribute' => array(

                array(tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY,
                    tubepress_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ', ),
            ),
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
