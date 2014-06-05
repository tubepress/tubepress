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
class tubepress_test_youtube_impl_listeners_video_YouTubeVideoConstructionListenerTest extends tubepress_test_TubePressUnitTest
{
    private static $_NAMESPACE_APP        = 'http://www.w3.org/2007/app';
    private static $_NAMESPACE_ATOM       = 'http://www.w3.org/2005/Atom';
    private static $_NAMESPACE_MEDIA      = 'http://search.yahoo.com/mrss/';
    private static $_NAMESPACE_YT         = 'http://gdata.youtube.com/schemas/2007';
    private static $_NAMESPACE_GD         = 'http://schemas.google.com/g/2005';

    /**
     * @var tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener
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

    private $_timezoneReset;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockTimeUtils = $this->mock(tubepress_core_util_api_TimeUtilsInterface::_);
        $this->_sut = new tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener($this->_mockExecutionContext, $this->_mockTimeUtils);

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

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_RANDOM_THUMBS)->andReturn(false);

        $event = $this->_prepareEvent($this->galleryXml(), 2);

        $this->_sut->onVideoConstruction($event);

        /**
         * @var $video tubepress_core_media_item_api_MediaItem
         */
        $video = $event->getSubject();
        $this->assertTrue($video instanceof tubepress_core_media_item_api_MediaItem);
        $this->assertEquals('PrankvsPrank', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_DISPLAY_NAME));
        $this->assertEquals('PrankvsPrank', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_AUTHOR_USER_ID));
        $this->assertEquals('Comedy', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_CATEGORY_DISPLAY_NAME));
        $this->assertEquals($this->_expectedDescription(), $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DESCRIPTION));
        $this->assertEquals('207', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_DURATION_SECONDS));
        $this->assertEquals('http://www.youtube.com/watch?v=J5nyQLr2zjc&feature=youtube_gdata', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_HOME_URL));
        $this->assertEquals('4.88243', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_AVERAGE));
        $this->assertEquals('35077', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_RATING_COUNT));
        $this->assertEquals('http://i.ytimg.com/vi/J5nyQLr2zjc/default.jpg', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_THUMBNAIL_URL));
        $this->assertEquals('unix time', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TIME_PUBLISHED_UNIXTIME));
        $this->assertEquals('SPAGHETTI AND MEAT BUGS PRANK', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE));
        $this->assertEquals('1571314', $video->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_VIEW_COUNT));
    }



    public function singleVideoXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/add-ons/youtube/resources/youtube/youtube-single-video.xml');
    }

    public function galleryXml()
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/add-ons/youtube/resources/youtube/youtube-gallery.xml');
    }

    /**
     * @param $feed
     * @param $index
     *
     * @return tubepress_core_event_impl_tickertape_EventBase
     */
    private function _prepareEvent($feed, $index)
    {
        $video = new tubepress_core_media_item_api_MediaItem('id');
        $mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->andReturn('youtube');
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockProvider);
        $doc = new DOMDocument();
        $doc->loadXML($feed);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('atom', self::$_NAMESPACE_ATOM);
        $xpath->registerNamespace('yt', self::$_NAMESPACE_YT);
        $xpath->registerNamespace('gd', self::$_NAMESPACE_GD);
        $xpath->registerNamespace('media', self::$_NAMESPACE_MEDIA);
        $xpath->registerNamespace('app', self::$_NAMESPACE_APP);

        $event = new tubepress_core_event_impl_tickertape_EventBase($video);

        $event->setArgument('domDocument', $doc);
        $event->setArgument('xPath', $xpath);
        $event->setArgument('rawFeed', $feed);
        $event->setArgument('zeroBasedFeedIndex', $index);

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
