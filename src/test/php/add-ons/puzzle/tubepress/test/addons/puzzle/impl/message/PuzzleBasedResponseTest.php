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
 * @covers tubepress_addons_puzzle_impl_message_PuzzleBasedResponse<extended>
 */
class tubepress_test_addons_puzzle_impl_message_PuzzleResponseTest extends tubepress_test_TubePressUnitTest
{
    public function testCanProvideCustomStatusCodeAndReasonPhrase()
    {
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(999, array(), null, array('reason_phrase' => 'hi!')));
        $this->assertEquals(999, $response->getStatusCode());
        $this->assertEquals('hi!', $response->getReasonPhrase());
    }

    public function testConvertsToString()
    {
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertEquals("HTTP/1.1 200 OK\r\n\r\n", (string) $response);
        // Add another header
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200, array('X-Test' => 'Guzzle')));
        $this->assertEquals("HTTP/1.1 200 OK\r\nX-Test: Guzzle\r\n\r\n", (string) $response);
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200, array('Content-Length' => 4), puzzle_stream_Stream::factory('test')));
        $this->assertEquals("HTTP/1.1 200 OK\r\nContent-Length: 4\r\n\r\ntest", (string) $response);
    }

    public function testConvertsToStringAndSeeksToByteZero()
    {
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200));
        $s = new tubepress_addons_puzzle_impl_stream_FlexibleStream(puzzle_stream_Stream::factory('foo'));
        $s->read(1);
        $response->setBody($s);
        $this->assertEquals("HTTP/1.1 200 OK\r\n\r\nfoo", (string) $response);
    }

    public function testParsesJsonResponses()
    {
        $json = '{"foo": "bar"}';
        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200, array(), puzzle_stream_Stream::factory($json)));
        $this->assertEquals(array('foo' => 'bar'), $response->toJson());
        $this->assertEquals(json_decode($json), $response->toJson(array('object' => true)));

        $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertEquals(null, $response->toJson());
    }

    public function testThrowsExceptionWhenFailsToParseJsonResponse()
    {
        try {
            $response = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200, array(), puzzle_stream_Stream::factory('{"foo": "')));
            $response->toJson();
        } catch (puzzle_exception_ParseException $e) {
            if (version_compare(PHP_VERSION, '5.3') >= 0) {
                $this->assertEquals('Unable to parse response body into JSON: 4', $e->getMessage());
            } else {
                $this->assertEquals('Unable to parse response body into JSON', $e->getMessage());
            }
            return;
        }
        $this->fail('Should have thrown exception');
    }

    public function testHasEffectiveUrl()
    {
        $r = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse(new puzzle_message_Response(200));
        $this->assertNull($r->getEffectiveUrl());
        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $r->setEffectiveUrl($mockUrl);
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $r->getEffectiveUrl());

        $r = new puzzle_message_Response(200);
        $r->setEffectiveUrl('http://bas.foo/sdf');
        $r = new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse($r);
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $r->getEffectiveUrl());
    }
}