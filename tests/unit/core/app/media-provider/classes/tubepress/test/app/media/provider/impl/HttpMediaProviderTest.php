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
 * @covers tubepress_app_media_provider_impl_HttpMediaProvider<extended>
 */
class tubepress_test_app_media_provider_impl_HttpMediaProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_media_provider_impl_HttpMediaProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEasyDelegate;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockEasyDelegate    = $this->mock('tubepress_app_media_provider_api_HttpProviderInterface');
        $this->_mockHttpClient      = $this->mock(tubepress_lib_http_api_HttpClientInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_app_media_provider_impl_HttpMediaProvider(

            $this->_mockEasyDelegate,
            $this->_mockLogger,
            $this->_mockEventDispatcher,
            $this->_mockHttpClient
        );
    }

    public function testFetchPage()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForPage')->once()->with(33)->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getTotalResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('getCurrentResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('canWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn(true);
        $this->_mockEasyDelegate->shouldReceive('getIdForItemAtIndex')->once()->with(0, 'body')->andReturn('id');

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);

        $item = $this->setupEventDispatcherForVideo();
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_PAGE, array(
            'provider' => $this->_sut,
            'pageNumber' => 33,
        ));

        $result = $this->_sut->fetchPage(33);

        $this->assertInstanceOf('tubepress_app_media_provider_api_Page', $result);
        $this->assertCount(1, $result->getItems());
        $items = $result->getItems();
        $this->assertSame($item, $items[0]);
    }

    public function testFetchPageNoWorkableVids()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForPage')->once()->with(33)->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getTotalResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('getCurrentResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('canWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn(false);
        $this->_mockEasyDelegate->shouldReceive('getReasonUnableToWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn('dunno');

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_PAGE, array(
            'pageNumber' => 33,
            'provider' => $this->_sut
        ));

        $result = $this->_sut->fetchPage(33);

        $this->assertInstanceOf('tubepress_app_media_provider_api_Page', $result);
        $this->assertCount(0, $result->getItems());
        $this->assertEquals(0, $result->getTotalResultCount());
    }

    public function testFetchPageNoResults()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForPage')->once()->with(33)->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getTotalResultCount')->once()->with('body')->andReturn(0);

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_PAGE, array(
            'pageNumber' => 33,
            'provider' => $this->_sut
        ));

        $result = $this->_sut->fetchPage(33);

        $this->assertInstanceOf('tubepress_app_media_provider_api_Page', $result);
        $this->assertCount(0, $result->getItems());
        $this->assertEquals(0, $result->getTotalResultCount());
    }

    public function testFetchSingle()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForSingle')->once()->with('x')->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getCurrentResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('canWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn(true);
        $this->_mockEasyDelegate->shouldReceive('getIdForItemAtIndex')->once()->with(0, 'body')->andReturn('id');

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_ITEM, array(
            'itemId' => 'x',
            'provider' => $this->_sut
        ));

        $item = $this->setupEventDispatcherForVideo();

        $result = $this->_sut->fetchSingle('x');

        $this->assertSame($item, $result);
    }

    private function _setupEventDispatcherForUrl(ehough_mockery_mockery_MockInterface $mockUrl, $eventName, array $additionalArgs)
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()
            ->with($mockUrl, $additionalArgs)->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            $eventName,
            $mockEvent
        );
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockUrl);
    }

    private function setupEventDispatcherForVideo()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()
            ->with(ehough_mockery_Mockery::type('tubepress_app_media_item_api_MediaItem'), array(
                'provider' => $this->_sut,
                'zeroBasedFeedIndex' => 0,
                'rawFeed' => 'body')
            )->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            $mockEvent
        );

        $this->_mockEasyDelegate->shouldReceive('onPreFireNewMediaItemEvent')->once()->with($mockEvent);

        $mockMediaItem = $this->mock('tubepress_app_media_item_api_MediaItem');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn($mockMediaItem);

        return $mockMediaItem;
    }

    public function testFetchSingleCannotWorkWith()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForSingle')->once()->with('x')->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getCurrentResultCount')->once()->with('body')->andReturn(1);
        $this->_mockEasyDelegate->shouldReceive('canWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn(false);
        $this->_mockEasyDelegate->shouldReceive('getReasonUnableToWorkWithItemAtIndex')->once()->with(0, 'body')->andReturn('dunno');

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_ITEM, array(
            'itemId' => 'x',
            'provider' => $this->_sut
        ));

        $result = $this->_sut->fetchSingle('x');

        $this->assertNull($result);
    }

    public function testFetchSingleNoResult()
    {
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('__toString')->atLeast(1)->andReturn('<url>');

        $this->_mockEasyDelegate->shouldReceive('buildUrlForSingle')->once()->with('x')->andReturn($mockUrl);
        $this->_mockEasyDelegate->shouldReceive('onAnalysisStart')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('onAnalysisComplete')->once()->with('body');
        $this->_mockEasyDelegate->shouldReceive('getCurrentResultCount')->once()->with('body')->andReturn(0);

        $mockResponse = $this->mock('tubepress_lib_http_api_message_ResponseInterface');
        $mockStream   = $this->mock('tubepress_lib_streams_api_StreamInterface');
        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockStream);
        $mockStream->shouldReceive('toString')->once()->andReturn('body');

        $mockRequest = $this->_getRequest($mockUrl);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);
        $this->_setupEventDispatcherForUrl($mockUrl, tubepress_app_media_provider_api_Constants::EVENT_URL_MEDIA_ITEM, array(
            'itemId' => 'x',
            'provider' => $this->_sut
        ));

        $result = $this->_sut->fetchSingle('x');

        $this->assertNull($result);
    }

    public function testRecognizesItem()
    {
        $this->_mockEasyDelegate->shouldReceive('recognizesItemId')->once()->with('a')->andReturn(true);
        $this->_mockEasyDelegate->shouldReceive('recognizesItemId')->once()->with('b')->andReturn(false);

        $this->assertTrue($this->_sut->recognizesItemId('a'));
        $this->assertFalse($this->_sut->recognizesItemId('b'));
    }

    /**
     * @dataProvider delegateData
     */
    public function testDelegation($methodName, $expected)
    {
        $this->_mockEasyDelegate->shouldReceive($methodName)->once()->andReturn($expected);
        $this->assertEquals($expected, $this->_sut->$methodName());
    }

    public function delegateData()
    {
        return array(

            array('getSearchModeName', 'bar'),
            array('getName', 'foo'),
            array('getDisplayName', 'friendly'),
            array('getGallerySourceNames', array('fuzz')),
            array('getSearchQueryOptionName', 'yo'),
            array('getMapOfFeedSortNamesToUntranslatedLabels', array('zz' => 'xx')),
            array('getMapOfPerPageSortNamesToUntranslatedLabels', array('zzz' => 'xxx')),

        );
    }

    /**
     * @param $mockUrl
     *
     * @return ehough_mockery_mockery_MockInterface
     */
    private function _getRequest($mockUrl)
    {
        $mockRequest = $this->mock('tubepress_lib_http_api_message_RequestInterface');
        $this->_mockHttpClient->shouldReceive('createRequest')->once()->with('GET', $mockUrl, ehough_mockery_Mockery::on(function ($opts) {

            if (!is_array($opts)) {

                return false;
            }

            return gettype($opts['debug']) === 'resource';

        }))->andReturn($mockRequest);
        $mockRequest->shouldReceive('getConfig')->once()->andReturn(array());
        $mockRequest->shouldReceive('setConfig')->once()->with(array('tubepress-remote-api-call' => true));

        return $mockRequest;
    }
}