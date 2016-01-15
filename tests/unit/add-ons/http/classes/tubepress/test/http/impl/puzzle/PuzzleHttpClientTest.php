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
 * @covers tubepress_http_impl_puzzle_PuzzleHttpClient
 */
class tubepress_test_http_impl_puzzle_PuzzleHttpClientTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_impl_puzzle_PuzzleHttpClient
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPuzzleClient;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEmitter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEmitter = $this->mock('puzzle_event_EmitterInterface');
        $this->_mockPuzzleClient = $this->mock('puzzle_Client');
        $this->_mockEventDispatcher = $this->mock(tubepress_api_event_EventDispatcherInterface::_);

        $this->_mockPuzzleClient->shouldReceive('setDefaultOption')->once()->with('verify', TUBEPRESS_ROOT . '/vendor/puzzlehttp/puzzle/src/main/php/puzzle/cacert.pem');

        $this->_mockPuzzleClient->shouldReceive('getEmitter')->atLeast(1)->andReturn($this->_mockEmitter);
        $this->_mockEmitter->shouldReceive('attach')->once()->with(ehough_mockery_Mockery::type('tubepress_http_impl_puzzle_PuzzleHttpClient'));

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_http_impl_puzzle_PuzzleHttpClient(

            $this->_mockEventDispatcher,
            $this->_mockPuzzleClient,
            $this->_mockLogger
        );
    }

    public function testGetDefaultOption()
    {
        $this->_mockPuzzleClient->shouldReceive('getDefaultOption')->once()->with('somekey')->andReturn('foobar');

        $result = $this->_sut->getDefaultOption('somekey');

        $this->assertEquals('foobar', $result);
    }

    public function testSetDefaultOption()
    {
        $this->_mockPuzzleClient->shouldReceive('setDefaultOption')->once()->with('somekey', 'foobar')->andReturn('foobar');

        $this->_sut->setDefaultOption('somekey', 'foobar');

        $this->assertTrue(true);
    }

    public function testSendException()
    {
        $mockBody           = $this->mock('tubepress_api_streams_StreamInterface');
        $mockRequest        = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockUrl            = $this->mock('tubepress_api_url_UrlInterface');
        $mockPuzzleResponse = $this->mock('puzzle_message_ResponseInterface');
        $mockPuzzleRequest  = $this->mock('puzzle_message_RequestInterface');

        $mockUrl->shouldReceive('toString')->once()->andReturn('http://foo.bar/z/y.php?test=false#frag');

        $mockRequest->shouldReceive('getMethod')->once()->andReturn('GET');
        $mockRequest->shouldReceive('getUrl')->once()->andReturn($mockUrl);
        $mockRequest->shouldReceive('getHeaders')->once()->andReturn(array('a' => 'b'));
        $mockRequest->shouldReceive('getBody')->once()->andReturn($mockBody);
        $mockRequest->shouldReceive('getConfig')->once()->andReturn(array('x' => 'boo'));

        $mockPuzzleRequest->shouldReceive('getUrl')->once()->andReturn('http://puzzle.url/some/thing.php');

        $this->_mockEmitter->shouldReceive('attach')->once()->with(ehough_mockery_Mockery::type('puzzle_subscriber_Prepare'));

        $mockPuzzleResponse->shouldReceive('getStatusCode')->once()->andReturn(502);
        $mockPuzzleResponse->shouldReceive('getEffectiveUrl')->once()->andReturn('http://mock.effective.url/bla');
        $this->_mockPuzzleClient->shouldReceive('send')->once()->with(ehough_mockery_Mockery::on(function ($r) {

            return $r instanceof puzzle_message_Request && $r->getMethod() === 'GET' && $r->getUrl() === 'http://foo.bar/z/y.php?test=false#frag';

        }))->andThrow(new puzzle_exception_RequestException('something bad', $mockPuzzleRequest, $mockPuzzleResponse));

        try {

            $this->_sut->___doSend($mockRequest);
        } catch (tubepress_api_http_exception_RequestException $e) {

            $this->assertEquals('something bad', $e->getMessage());
            $this->assertTrue($e->getRequest() instanceof tubepress_api_http_message_RequestInterface);
            $this->assertTrue($e->getResponse() instanceof tubepress_api_http_message_ResponseInterface);
            return;
        }

        $this->fail('Did not throw correct exception');
    }

    public function testSendNormal()
    {
        $mockPuzzleBody = $this->mock('puzzle_stream_StreamInterface');
        $mockConfig = $this->mock('puzzle_Collection');
        $mockConfig->shouldReceive('toArray')->once()->andReturn(array('some' => 'config'));
        $mockPuzzleRequest = $this->_setupMocksForCreateRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2), 3);
        $mockPuzzleRequest->shouldReceive('getHeaders')->times(2)->andReturn(array('foo' => 'bar'));
        $mockPuzzleRequest->shouldReceive('getBody')->once()->andReturn($mockPuzzleBody);
        $mockPuzzleRequest->shouldReceive('getConfig')->once()->andReturn($mockConfig);

        $mockPuzzleBody = $this->mock('puzzle_stream_StreamInterface');
        $mockPuzzleBody->shouldReceive('__toString')->once()->andReturn('x');

        $mockPuzzleResponse = $this->mock('puzzle_message_ResponseInterface');
        $mockPuzzleResponse->shouldReceive('getEffectiveUrl')->once()->andReturn('http://bar.foo/z/abc.php');
        $mockPuzzleResponse->shouldReceive('getHeaders')->once()->andReturn(array('boo' => 'foo'));
        $mockPuzzleResponse->shouldReceive('getBody')->once()->andReturn($mockPuzzleBody);
        $this->_mockPuzzleClient->shouldReceive('send')->once()->with(ehough_mockery_Mockery::on(function ($r) {

            return $r instanceof puzzle_message_Request && $r->getMethod() === 'GET' && $r->getUrl() === 'http://foo.bar/z/y.php?test=false#frag';

        }))->andReturn($mockPuzzleResponse);

        $this->_mockEmitter->shouldReceive('attach')->once()->with(ehough_mockery_Mockery::type('puzzle_subscriber_Prepare'));

        $mockRequestEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockRequestEvent->shouldReceive('hasArgument')->once()->with('response')->andReturn(true);
        $mockRequestEvent->shouldReceive('getArgument')->once()->with('response')->andReturnNull();
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('tubepress_http_impl_puzzle_PuzzleBasedRequest'), array('response' => null))->andReturn($mockRequestEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_REQUEST, $mockRequestEvent);

        $mockTubePressResponse = $this->mock('tubepress_api_http_message_ResponseInterface');

        $mockResponseEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockResponseEvent->shouldReceive('getSubject')->once()->andReturn($mockTubePressResponse);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('tubepress_http_impl_puzzle_PuzzleBasedResponse'), ehough_mockery_Mockery::on(function ($arr) {

            return is_array($arr) && $arr['request'] instanceof tubepress_api_http_message_RequestInterface;

        }))->andReturn($mockResponseEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_http_Events::EVENT_HTTP_RESPONSE, $mockResponseEvent);

        $response = $this->_sut->get('http://foo.bar/z/y.php?test=false#frag', array('one' => 2));

        $this->assertSame($mockTubePressResponse, $response);
    }

    public function testCreateRequest()
    {
        $this->_setupMocksForCreateRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2), 1);

        $request = $this->_sut->createRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2));

        $this->assertInstanceOf('tubepress_api_http_message_RequestInterface', $request);
        $this->assertEquals('http://foo.bar/z/y.php?test=false#frag', $request->getUrl()->toString());
        $this->assertEquals('GET', $request->getMethod());
    }

    private function _setupMocksForCreateRequest($method, $url, $options, $getMethodCount)
    {
        $mockPuzzleRequest = $this->mock('puzzle_message_RequestInterface');
        $mockPuzzleRequest->shouldReceive('getUrl')->atLeast(1)->andReturn("$url");
        $mockPuzzleRequest->shouldReceive('getMethod')->times($getMethodCount)->andReturn($method);

        $this->_mockPuzzleClient->shouldReceive('createRequest')->once()->with($method, "$url", $options)->andReturn($mockPuzzleRequest);

        return $mockPuzzleRequest;
    }
}