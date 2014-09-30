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
 * @covers tubepress_youtube2_impl_listeners_media_HttpItemListener
 */
class tubepress_test_youtube2_impl_listeners_media_HttpItemListenerTest extends tubepress_test_TubePressUnitTest
{
    private static $_NAMESPACE_APP        = 'http://www.w3.org/2007/app';
    private static $_NAMESPACE_ATOM       = 'http://www.w3.org/2005/Atom';
    private static $_NAMESPACE_MEDIA      = 'http://search.yahoo.com/mrss/';
    private static $_NAMESPACE_YT         = 'http://gdata.youtube.com/schemas/2007';
    private static $_NAMESPACE_GD         = 'http://schemas.google.com/g/2005';

    /**
     * @var tubepress_youtube2_impl_listeners_media_HttpItemListener
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAttributeFormatter;

    private $_timezoneReset;

    public function onSetup()
    {
        $this->_mockExecutionContext   = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockTimeUtils          = $this->mock(tubepress_lib_api_util_TimeUtilsInterface::_);
        $this->_mockAttributeFormatter = $this->mock(tubepress_app_api_media_AttributeFormatterInterface::_);

        $this->_sut = new tubepress_youtube2_impl_listeners_media_HttpItemListener(

            $this->_mockAttributeFormatter,
            $this->_mockTimeUtils,
            $this->_mockExecutionContext
        );

        $this->_timezoneReset = date_default_timezone_get();
    }

    public function onTearDown()
    {
        date_default_timezone_set($this->_timezoneReset);
    }

    public function testConstructionGalleryXmlStaticThumbAbsoluteDates()
    {
        $this->_mockTimeUtils->shouldReceive('rfc3339toUnixTime')->once()->with('2012-09-04T19:11:31.000Z')->andReturn('unix time');

        date_default_timezone_set('America/New_York');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS)->andReturn(false);

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onHttpItem($event);

        /**
         * @var $video tubepress_app_api_media_MediaItem
         */
        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_app_api_media_MediaItem);
        $this->assertEquals('PrankvsPrank', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('PrankvsPrank', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('Comedy', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_CATEGORY_DISPLAY_NAME));
        $this->assertEquals($this->_expectedDescription(), $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('207', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('http://www.youtube.com/watch?v=J5nyQLr2zjc&feature=youtube_gdata', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_HOME_URL));
        $this->assertEquals('4.88243', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE));
        $this->assertEquals('35077', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT));
        $this->assertEquals('http://i.ytimg.com/vi/J5nyQLr2zjc/default.jpg', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('unix time', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('SPAGHETTI AND MEAT BUGS PRANK', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_TITLE));
        $this->assertEquals('1571314', $video->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT));
    }



    public function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/youtube_v2/resources/youtube/youtube-single-video.xml');
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/youtube_v2/resources/youtube/youtube-gallery.xml');
    }

    /**
     * @param $feed
     * @param $index
     *
     * @return tubepress_lib_impl_event_tickertape_EventBase
     */
    private function _prepareEvent($feed, $index)
    {
        $mockMediaItem = new tubepress_app_api_media_MediaItem('id');
        $mockProvider = $this->mock(tubepress_app_api_media_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->andReturn('youtube');
        $doc = new DOMDocument();
        $doc->loadXML($feed);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('atom', self::$_NAMESPACE_ATOM);
        $xpath->registerNamespace('yt', self::$_NAMESPACE_YT);
        $xpath->registerNamespace('gd', self::$_NAMESPACE_GD);
        $xpath->registerNamespace('media', self::$_NAMESPACE_MEDIA);
        $xpath->registerNamespace('app', self::$_NAMESPACE_APP);

        $event = new tubepress_lib_impl_event_tickertape_EventBase($mockMediaItem);

        $event->setArgument('domDocument', $doc);
        $event->setArgument('xpath', $xpath);
        $event->setArgument('zeroBasedIndex', $index);

        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_COUNT, 0);

        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, tubepress_app_api_media_MediaItem::ATTRIBUTE_VIEW_COUNT, 0);

        $this->_mockAttributeFormatter->shouldReceive('formatNumberAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE, tubepress_app_api_media_MediaItem::ATTRIBUTE_RATING_AVERAGE, 2);

        $this->_mockAttributeFormatter->shouldReceive('truncateStringAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_media_MediaItem::ATTRIBUTE_DESCRIPTION, tubepress_app_api_options_Names::META_DESC_LIMIT);

        $this->_mockAttributeFormatter->shouldReceive('formatDurationAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_SECONDS, tubepress_app_api_media_MediaItem::ATTRIBUTE_DURATION_FORMATTED);

        $this->_mockAttributeFormatter->shouldReceive('formatDateAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME, tubepress_app_api_media_MediaItem::ATTRIBUTE_TIME_PUBLISHED_FORMATTED);

        $this->_mockAttributeFormatter->shouldReceive('implodeArrayAttribute')->once()->with($mockMediaItem,
            tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORD_ARRAY, tubepress_app_api_media_MediaItem::ATTRIBUTE_KEYWORDS_FORMATTED, ', ');

        return $event;
    }

    private function _expectedDescription()
    {
        return <<<EOT
Find it on Reddit - http://bit.ly/TWtmtl

I stuffed crickets into Jeana's meatballs. This was the line that should have never been crossed.  Feeding her bugs.  Team Jesse FTW.  Be sure to click the Thumbs Up for crossing the line. lol

Facebook: http://facebook.com/prankvsprank

Follow us on Twitter:
http://twitter.com/PhillyChic5
http://twitter.com/JesseWelle

Jesse's Google + http://bit.ly/qhVCJ4

T-shirts http://districtlines.com/prankvsprank

Our Mailing address:
PrankvsPrank
P.O. Box 2106
Voorhees, NJ 08043
EOT;

    }
}
