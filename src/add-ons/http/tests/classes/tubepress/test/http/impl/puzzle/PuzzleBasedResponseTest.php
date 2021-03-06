<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_impl_puzzle_PuzzleBasedResponse<extended>
 */
class tubepress_test_http_impl_puzzle_PuzzleBasedResponseTest extends tubepress_api_test_TubePressUnitTest
{
    public function testCanProvideCustomStatusCodeAndReasonPhrase()
    {
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(999, array(), null, array('reason_phrase' => 'hi!')));
        $this->assertEquals(999, $response->getStatusCode());
        $this->assertEquals('hi!', $response->getReasonPhrase());
    }

    public function testConvertsToString()
    {
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertEquals("HTTP/1.1 200 OK\r\n\r\n", (string) $response);
        // Add another header
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200, array('X-Test' => 'Guzzle')));
        $this->assertEquals("HTTP/1.1 200 OK\r\nX-Test: Guzzle\r\n\r\n", (string) $response);
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200, array('Content-Length' => 4), puzzle_stream_Stream::factory('test')));
        $this->assertEquals("HTTP/1.1 200 OK\r\nContent-Length: 4\r\n\r\ntest", (string) $response);
    }

    public function testConvertsToStringAndSeeksToByteZero()
    {
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200));
        $s        = new tubepress_http_impl_puzzle_streams_PuzzleBasedStream(puzzle_stream_Stream::factory('foo'));
        $s->read(1);
        $response->setBody($s);
        $this->assertEquals("HTTP/1.1 200 OK\r\n\r\nfoo", (string) $response);
    }

    public function testParsesJsonResponses()
    {
        $json     = '{"foo": "bar"}';
        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200, array(), puzzle_stream_Stream::factory($json)));
        $this->assertEquals(array('foo' => 'bar'), $response->toJson());
        $this->assertEquals(json_decode($json), $response->toJson(array('object' => true)));

        $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertEquals(null, $response->toJson());
    }

    public function testThrowsExceptionWhenFailsToParseJsonResponse()
    {
        try {
            $response = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200, array(), puzzle_stream_Stream::factory('{"foo": "')));
            $response->toJson();
        } catch (puzzle_exception_ParseException $e) {
            if (version_compare(PHP_VERSION, '7.0') >= 0) {
                $this->assertEquals('Unable to parse JSON data: JSON_ERROR_CTRL_CHAR - Unexpected control character found', $e->getMessage());
            } else {
                $this->assertEquals('Unable to parse JSON data: JSON_ERROR_SYNTAX - Syntax error, malformed JSON', $e->getMessage());
            }

            return;
        }
        $this->fail('Should have thrown exception');
    }

    public function testHasEffectiveUrl()
    {
        $r = new tubepress_http_impl_puzzle_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertNull($r->getEffectiveUrl());
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $r->setEffectiveUrl($mockUrl);
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $r->getEffectiveUrl());

        $r = new puzzle_message_Response(200);
        $r->setEffectiveUrl('http://bas.foo/sdf');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedResponse($r);
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $r->getEffectiveUrl());
    }
}
