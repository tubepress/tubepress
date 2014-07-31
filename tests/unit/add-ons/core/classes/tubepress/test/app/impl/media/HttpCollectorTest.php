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
 * @covers tubepress_app_impl_media_HttpCollector
 */
class tubepress_test_app_api_media_HttpCollectorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFeedHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrl;

    /**
     * @var tubepress_app_impl_media_HttpCollector
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockHttpClient      = $this->mock(tubepress_lib_api_http_HttpClientInterface::_);
        $this->_mockLogger          = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockFeedHandler     = $this->mock(tubepress_app_api_media_HttpFeedHandlerInterface::_);
        $this->_mockUrl             = $this->mock(tubepress_platform_api_url_UrlInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_app_impl_media_HttpCollector(
            $this->_mockLogger,
            $this->_mockEventDispatcher,
            $this->_mockHttpClient
        );
    }

    public function testFetchSingle()
    {
        $this->_setupHttpClient(tubepress_app_api_event_Events::MEDIA_ITEM_URL, array(

            'provider' => $this->_sut,
            'itemId'   => 'item fun',
        ));

        $this->_mockFeedHandler->shouldReceive('buildUrlForItem')->once()->with('item fun')->andReturn($this->_mockUrl);
        $this->_mockFeedHandler->shouldReceive('onAnalysisStart')->once()->with('abc');
        $this->_mockFeedHandler->shouldReceive('getCurrentResultCount')->once()->andReturn(1);
        $this->_mockFeedHandler->shouldReceive('getReasonUnableToUseItemAtIndex')->once()->with(0)->andReturnNull();
        $this->_mockFeedHandler->shouldReceive('getIdForItemAtIndex')->once()->with(0)->andReturn('some cool item');
        $this->_mockFeedHandler->shouldReceive('getNewItemEventArguments')->once()
            ->with(ehough_mockery_Mockery::type('tubepress_app_api_media_MediaItem'), 0)->andReturn(array('event' => 'args'));
        $this->_mockFeedHandler->shouldReceive('onAnalysisComplete')->once();

        $this->_setupNewHttpItemEvent();

        /**
         * @var $result tubepress_app_api_media_MediaItem
         */
        $result = $this->_sut->collectSingle('item fun', $this->_mockFeedHandler);

        $this->assertEquals('hiya', $result);
    }

    public function testFetchPage()
    {
        $this->_setupHttpClient(tubepress_app_api_event_Events::MEDIA_PAGE_URL, array(

            'pageNumber' => 33,
            'provider' => $this->_sut
        ));

        $this->_mockFeedHandler->shouldReceive('buildUrlForPage')->once()->with(33)->andReturn($this->_mockUrl);
        $this->_mockFeedHandler->shouldReceive('onAnalysisStart')->once()->with('abc');
        $this->_mockFeedHandler->shouldReceive('getTotalResultCount')->once()->andReturn(22);
        $this->_mockFeedHandler->shouldReceive('getCurrentResultCount')->once()->andReturn(1);
        $this->_mockFeedHandler->shouldReceive('getReasonUnableToUseItemAtIndex')->once()->with(0)->andReturnNull();
        $this->_mockFeedHandler->shouldReceive('getIdForItemAtIndex')->once()->with(0)->andReturn('some cool item');
        $this->_mockFeedHandler->shouldReceive('getNewItemEventArguments')->once()
            ->with(ehough_mockery_Mockery::type('tubepress_app_api_media_MediaItem'), 0)->andReturn(array('event' => 'args'));
        $this->_mockFeedHandler->shouldReceive('onAnalysisComplete')->once();

        $this->_setupNewHttpItemEvent();

        $result = $this->_sut->collectPage(33, $this->_mockFeedHandler);

        $this->assertInstanceOf('tubepress_app_api_media_MediaPage', $result);
        $this->assertEquals(22, $result->getTotalResultCount());
        $items = $result->getItems();
        $this->assertTrue(is_array($items));
        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertEquals('hiya', $item);
    }

    private function _setupNewHttpItemEvent()
    {
        $mockNewItemEvent = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()
            ->with(ehough_mockery_Mockery::type('tubepress_app_api_media_MediaItem'), array('event' => 'args'))
            ->andReturn($mockNewItemEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_app_api_event_Events::MEDIA_ITEM_HTTP_NEW, $mockNewItemEvent
        );

        $mockNewItemEvent->shouldReceive('getSubject')->once()->andReturn('hiya');
    }

    private function _setupHttpClient($eventName, array $eventArgs)
    {
        $mockUrlEvent     = $this->mock('tubepress_lib_api_event_EventInterface');

        $mockHttpRequest  = $this->mock('tubepress_lib_api_http_message_RequestInterface');
        $mockHttpResponse = $this->mock('tubepress_lib_api_http_message_ResponseInterface');
        $mockStream       = $this->mock('tubepress_lib_api_streams_StreamInterface');

        $mockHttpRequest->shouldReceive('getConfig')->once()->andReturn(array('hi' => 'there'));
        $mockHttpRequest->shouldReceive('setConfig')->once()->with(array(
            'hi' => 'there',
            'tubepress-remote-api-call' => true
        ));

        $mockHttpResponse->shouldReceive('getBody')->once()->andReturn($mockStream);

        $mockStream->shouldReceive('toString')->once()->andReturn('abc');

        $this->_mockHttpClient->shouldReceive('createRequest')->once()->with('GET', $this->_mockUrl, ehough_mockery_Mockery::on(function ($opts) {

            if (!is_array($opts)) {

                return false;
            }

            if (count($opts) !== 1) {

                return false;
            }

            $stream = $opts['debug'];

            if (!is_resource($stream)) {

                return false;
            }

            fwrite($stream, 'hello!');

            return true;
        }))->andReturn($mockHttpRequest);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockHttpRequest)->andReturn($mockHttpResponse);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($this->_mockUrl, $eventArgs)->andReturn($mockUrlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            $eventName, $mockUrlEvent
        );

        $mockUrlEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockUrl);

        $this->_mockUrl->shouldReceive('__toString')->twice()->andReturn('url as string');
    }
}