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
 * @covers tubepress_http_impl_puzzle_PuzzleBasedRequest<extended>
 */
class tubepress_test_http_impl_puzzle_PuzzleBasedRequestTest extends tubepress_api_test_TubePressUnitTest
{
    public function testConfig()
    {
        $r = new puzzle_message_Request('PUT', '/test', array('test' => '123'), puzzle_stream_Stream::factory('foo'));
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $this->assertEquals(array(), $r->getConfig());

        $r->setConfig(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $r->getConfig());

        $r->setConfig(array('fooz' => 'barz'));
        $this->assertEquals(array('fooz' => 'barz'), $r->getConfig());
    }

    public function testConstructorInitializesMessage()
    {
        $r = new puzzle_message_Request('PUT', '/test', array('test' => '123'), puzzle_stream_Stream::factory('foo'));
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);
        $this->assertEquals('PUT', $r->getMethod());
        $this->assertEquals('/test', $r->getUrl());
        $this->assertEquals('123', $r->getHeader('test'));
        $this->assertEquals('foo', $r->getBody());
    }

    public function testConstructorInitializesMessageWithProtocolVersion()
    {
        $r = new puzzle_message_Request('GET', '', array(), null, array('protocol_version' => 10));
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);
        $this->assertEquals(10, $r->getProtocolVersion());
    }

    public function testCastsToString()
    {
        $r = new puzzle_message_Request('GET', 'http://test.com/test', array('foo' => 'baz'), puzzle_stream_Stream::factory('body'));
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);
        $s = explode("\r\n", (string) $r);
        $this->assertEquals("GET /test HTTP/1.1", $s[0]);
        $this->assertContains('Host: test.com', $s);
        $this->assertContains('foo: baz', $s);
        $this->assertContains('', $s);
        $this->assertContains('body', $s);
    }

    public function testSettingUrlOverridesHostHeaders()
    {
        $r = new puzzle_message_Request('GET', 'http://test.com/test');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);
        $mockUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockUrl->shouldReceive('getHost')->once()->andReturn('baz.com');
        $mockUrl->shouldReceive('getPath')->once()->andReturn('/bar');
        $mockUrl->shouldReceive('getScheme')->once()->andReturn('https');
        $r->setUrl($mockUrl);
        $this->assertEquals('baz.com', $r->getUrl()->getHost());
        $this->assertEquals('/bar', $r->getUrl()->getPath());
        $this->assertEquals('https', $r->getUrl()->getScheme());
    }

    public function testQueryIsMutable()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com?baz=bar');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);
        $this->assertEquals('baz=bar', $r->getUrl()->getQuery()->toString());
        $this->assertInstanceOf('tubepress_api_url_QueryInterface', $r->getUrl()->getQuery());
    }

    public function testQueryCanChange()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com?baz=bar');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $r->getUrl()->setQuery(new tubepress_url_impl_puzzle_PuzzleBasedQuery(new puzzle_Query(array('foo' => 'bar'))));
        $this->assertEquals('foo=bar', $r->getUrl()->getQuery()->toString());
    }

    public function testCanChangeMethod()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $r->setMethod('put');
        $this->assertEquals('PUT', $r->getMethod());
    }

    public function testCanChangeSchemeWithPort()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com:80');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $r->getUrl()->setScheme('https');
        $this->assertEquals('https://www.foo.com', $r->getUrl()->toString());
    }

    public function testCanChangeScheme()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $r->getUrl()->setScheme('https');
        $this->assertEquals('https://www.foo.com', $r->getUrl());
    }

    public function testCanChangeHost()
    {
        $r = new puzzle_message_Request('GET', 'http://www.foo.com:222');
        $r = new tubepress_http_impl_puzzle_PuzzleBasedRequest($r);

        $r->getUrl()->setHost('goo');
        $this->assertEquals('http://goo:222', $r->getUrl());
        $r->getUrl()->setHost('goo:80');
        $this->assertEquals('http://goo', $r->getUrl());
    }
}