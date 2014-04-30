<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of coauthor (https://github.com/ehough/coauthor)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_puzzle_impl_http_PuzzleHttpClient
 */
class tubepress_test_addons_puzzle_impl_http_PuzzleHttpClientTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_puzzle_impl_http_PuzzleHttpClient
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPuzzleClient;

    public function onSetup()
    {
        $this->_mockPuzzleClient = ehough_mockery_Mockery::mock('puzzle_ClientInterface');

        $this->_sut = new tubepress_addons_puzzle_impl_http_PuzzleHttpClient($this->_mockPuzzleClient);
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
        $mockBody = ehough_mockery_Mockery::mock('tubepress_api_stream_StreamInterface');
        $mockConfig = ehough_mockery_Mockery::mock('puzzle_Collection');
        $mockConfig->shouldReceive('toArray')->once()->andReturn(array('some' => 'config'));
        $request = $this->_setupMocksForCreateRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2));
        $request->shouldReceive('getHeaders')->once()->andReturn(array('foo' => 'bar'));
        $request->shouldReceive('getBody')->once()->andReturn($mockBody);
        $request->shouldReceive('getConfig')->once()->andReturn($mockConfig);

        $mockResponse = ehough_mockery_Mockery::mock('puzzle_message_ResponseInterface');
        $mockResponse->shouldReceive('getEffectiveUrl')->once()->andReturn('http://bar.foo/z/abc.php');
        $mockResponse->shouldReceive('getStatusCode')->once()->andReturn(200);
        $this->_mockPuzzleClient->shouldReceive('send')->once()->with(ehough_mockery_Mockery::on(function ($r) {

            return $r instanceof puzzle_message_Request && $r->getMethod() === 'GET' && $r->getUrl() === 'http://foo.bar/z/y.php?test=false#frag';

        }))->andThrow(new puzzle_exception_RequestException('something bad', $request, $mockResponse));

        try {

            $this->_sut->get('http://foo.bar/z/y.php?test=false#frag', array('one' => 2));
        } catch (tubepress_spi_http_RequestException $e) {

            $this->assertEquals('something bad', $e->getMessage());
            $this->assertTrue($e->getRequest() instanceof tubepress_api_http_RequestInterface);
            $this->assertTrue($e->getResponse() instanceof tubepress_api_http_ResponseInterface);
            return;
        }

        $this->fail('Did not throw correct exception');
    }

    public function testSendNormal()
    {
        $mockBody = ehough_mockery_Mockery::mock('tubepress_api_stream_StreamInterface');
        $mockConfig = ehough_mockery_Mockery::mock('puzzle_Collection');
        $mockConfig->shouldReceive('toArray')->once()->andReturn(array('some' => 'config'));
        $request = $this->_setupMocksForCreateRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2));
        $request->shouldReceive('getHeaders')->once()->andReturn(array('foo' => 'bar'));
        $request->shouldReceive('getBody')->once()->andReturn($mockBody);
        $request->shouldReceive('getConfig')->once()->andReturn($mockConfig);

        $mockResponse = ehough_mockery_Mockery::mock('puzzle_message_ResponseInterface');
        $mockResponse->shouldReceive('getEffectiveUrl')->once()->andReturn('http://bar.foo/z/abc.php');
        $mockResponse->shouldReceive('getStatusCode')->once()->andReturn(200);
        $this->_mockPuzzleClient->shouldReceive('send')->once()->with(ehough_mockery_Mockery::on(function ($r) {

            return $r instanceof puzzle_message_Request && $r->getMethod() === 'GET' && $r->getUrl() === 'http://foo.bar/z/y.php?test=false#frag';

        }))->andReturn($mockResponse);

        $response = $this->_sut->get('http://foo.bar/z/y.php?test=false#frag', array('one' => 2));

        $this->assertInstanceOf('tubepress_api_http_ResponseInterface', $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateRequest()
    {
        $this->_setupMocksForCreateRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2));

        $request = $this->_sut->createRequest('GET', 'http://foo.bar/z/y.php?test=false#frag', array('one' => 2));

        $this->assertInstanceOf('tubepress_api_http_RequestInterface', $request);
        $this->assertEquals('http://foo.bar/z/y.php?test=false#frag', $request->getUrl()->toString());
        $this->assertEquals('GET', $request->getMethod());
    }

    private function _setupMocksForCreateRequest($method, $url, $options)
    {
        $mockPuzzleRequest = ehough_mockery_Mockery::mock('puzzle_message_RequestInterface');
        $mockPuzzleRequest->shouldReceive('getUrl')->atLeast(1)->andReturn("$url");
        $mockPuzzleRequest->shouldReceive('getMethod')->once()->andReturn($method);

        $this->_mockPuzzleClient->shouldReceive('createRequest')->once()->with($method, "$url", $options)->andReturn($mockPuzzleRequest);

        return $mockPuzzleRequest;
    }
}