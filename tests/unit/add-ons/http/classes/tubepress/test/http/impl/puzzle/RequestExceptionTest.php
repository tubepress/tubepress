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
 * @covers tubepress_http_impl_puzzle_RequestException
 */
class tubepress_test_http_impl_puzzle_RequestExceptionTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var puzzle_exception_RequestException
     */
    private $_mockPuzzleException;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPuzzleRequest;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPuzzleResponse;

    /**
     * @var tubepress_http_impl_puzzle_RequestException
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockPuzzleRequest   = $this->mock('puzzle_message_RequestInterface');
        $this->_mockPuzzleResponse  = $this->mock('puzzle_message_ResponseInterface');
        $this->_mockPuzzleResponse->shouldReceive('getStatusCode')->times(3)->andReturn(404);
        $this->_mockPuzzleResponse->shouldReceive('getReasonPhrase')->once()->andReturn('some reason!');
        $this->_mockPuzzleResponse->shouldReceive('getEffectiveUrl')->once()->andReturn('http://bar.foo/z/y.php?test=true#frag');
        $this->_mockPuzzleRequest->shouldReceive('getUrl')->twice()->andReturn('http://foo.bar/z/y.php?test=true#frag');
        $this->_mockPuzzleException = puzzle_exception_RequestException::create(
            $this->_mockPuzzleRequest,
            $this->_mockPuzzleResponse
        );
        $this->_sut = new tubepress_http_impl_puzzle_RequestException($this->_mockPuzzleException);
    }

    public function testGetRequest()
    {
        $request = $this->_sut->getRequest();

        $this->assertInstanceOf('tubepress_api_http_message_RequestInterface', $request);
    }

    public function testGetResponse()
    {
        $request = $this->_sut->getResponse();

        $this->assertInstanceOf('tubepress_api_http_message_ResponseInterface', $request);

        $url = $request->getEffectiveUrl();
        $this->assertEquals('http://bar.foo/z/y.php?test=true#frag', "$url");
    }
}