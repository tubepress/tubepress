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
class tubepress_test_http_impl_puzzle_MessageTest extends tubepress_test_TubePressUnitTest
{
    public function testHasProtocolVersion()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $this->assertEquals(1.1, $m->getProtocolVersion());
    }

    public function testHasHeaders()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $this->assertFalse($m->hasHeader('foo'));
        $m->addHeader('foo', 'bar');
        $this->assertTrue($m->hasHeader('foo'));
    }

    public function testInitializesMessageWithProtocolVersionOption()
    {
        $m = new puzzle_message_Request('GET', '/', array(), null, array(
            'protocol_version' => '10'
        ));
        $this->assertEquals(10, $m->getProtocolVersion());
    }

    public function testHasBody()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $this->assertNull($m->getBody());
        $s = new tubepress_http_impl_puzzle_streams_PuzzleBasedStream(puzzle_stream_Stream::factory('test'));
        $m->setBody($s);
        $actualBody = $m->getBody();
        $this->assertInstanceOf('tubepress_lib_api_streams_StreamInterface', $actualBody);
        $this->assertEquals('test', "$actualBody");
        $this->assertFalse($m->hasHeader('Content-Length'));
    }

    public function testCanRemoveBodyBySettingToNullAndRemovesCommonBodyHeaders()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $m->setBody(new tubepress_http_impl_puzzle_streams_PuzzleBasedStream(puzzle_stream_Stream::factory('foo')));
        $m->setHeader('Content-Length', 3)->setHeader('Transfer-Encoding', 'chunked');
        $m->setBody(null);
        $this->assertNull($m->getBody());
        $this->assertFalse($m->hasHeader('Content-Length'));
        $this->assertFalse($m->hasHeader('Transfer-Encoding'));
    }

    public function testCastsToString()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $m->setHeader('foo', 'bar');
        $m->setBody(new tubepress_http_impl_puzzle_streams_PuzzleBasedStream(puzzle_stream_Stream::factory('baz')));
        $this->assertEquals("GET / HTTP/1.1\r\nfoo: bar\r\n\r\nbaz", (string) $m);
    }

    public function testAddsHeadersWhenNotPresent()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeader('foo', 'bar');
        $this->assertInternalType('string', $h->getHeader('foo'));
        $this->assertEquals('bar', $h->getHeader('foo'));
    }

    public function testAddsHeadersWhenPresentSameCase()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeader('foo', 'bar')->addHeader('foo', 'baz');
        $this->assertEquals('bar, baz', $h->getHeader('foo'));
        $this->assertEquals(array('bar', 'baz'), $h->getHeader('foo', true));
    }

    public function testAddsMultipleHeaders()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeaders(array(
            'foo' => ' bar',
            'baz' => array(' bam ', 'boo')
        ));
        $this->assertEquals(array(
            'foo' => array('bar'),
            'baz' => array('bam', 'boo')
        ), $h->getHeaders());
    }

    public function testAddsHeadersWhenPresentDifferentCase()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeader('Foo', 'bar')->addHeader('fOO', 'baz');
        $this->assertEquals('bar, baz', $h->getHeader('foo'));
    }

    public function testAddsHeadersWithArray()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeader('Foo', array('bar', 'baz'));
        $this->assertEquals('bar, baz', $h->getHeader('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionWhenInvalidValueProvidedToAddHeader()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $m->addHeader('foo', false);
    }

    public function testGetHeadersReturnsAnArrayOfOverTheWireHeaderValues()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->addHeader('foo', 'bar');
        $h->addHeader('Foo', 'baz');
        $h->addHeader('boO', 'test');
        $result = $h->getHeaders();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('Foo', $result);
        $this->assertArrayNotHasKey('foo', $result);
        $this->assertArrayHasKey('boO', $result);
        $this->assertEquals(array('bar', 'baz'), $result['Foo']);
        $this->assertEquals(array('test'), $result['boO']);
    }

    public function testSetHeaderOverwritesExistingValues()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', 'bar');
        $this->assertEquals('bar', $h->getHeader('foo'));
        $h->setHeader('Foo', 'baz');
        $this->assertEquals('baz', $h->getHeader('foo'));
        $this->assertArrayHasKey('Foo', $h->getHeaders());
    }

    public function testSetHeaderOverwritesExistingValuesUsingHeaderArray()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', array('bar'));
        $this->assertEquals('bar', $h->getHeader('foo'));
    }

    public function testSetHeaderOverwritesExistingValuesUsingArray()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', array('bar'));
        $this->assertEquals('bar', $h->getHeader('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionWhenInvalidValueProvidedToSetHeader()
    {
        $m = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $m->setHeader('foo', false);
    }

    public function testSetHeadersOverwritesAllHeaders()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', 'bar');
        $h->setHeaders(array('foo' => 'a', 'boo' => 'b'));
        $this->assertEquals(array('foo' => array('a'), 'boo' => array('b')), $h->getHeaders());
    }

    public function testChecksIfCaseInsensitiveHeaderIsPresent()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', 'bar');
        $this->assertTrue($h->hasHeader('foo'));
        $this->assertTrue($h->hasHeader('Foo'));
        $h->setHeader('fOo', 'bar');
        $this->assertTrue($h->hasHeader('Foo'));
    }

    public function testRemovesHeaders()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', 'bar');
        $h->removeHeader('foo');
        $this->assertFalse($h->hasHeader('foo'));
        $h->setHeader('Foo', 'bar');
        $h->removeHeader('FOO');
        $this->assertFalse($h->hasHeader('foo'));
    }

    public function testReturnsCorrectTypeWhenMissing()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $this->assertInternalType('string', $h->getHeader('foo'));
        $this->assertInternalType('array', $h->getHeader('foo', true));
    }

    public function testSetsIntegersAndFloatsAsHeaders()
    {
        $h = new tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo(new tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar());
        $h->setHeader('foo', 10);
        $h->setHeader('bar', 10.5);
        $h->addHeader('foo', 10);
        $h->addHeader('bar', 10.5);
        $this->assertSame('10, 10', $h->getHeader('foo'));
        $this->assertSame('10.5, 10.5', $h->getHeader('bar'));
    }
}

class tubepress_test_lib_http_impl_puzzle_message_MessageTest__bar extends puzzle_message_Request
{
    public function __construct()
    {
        parent::__construct('GET', '');
    }
}

class tubepress_test_lib_http_impl_puzzle_message_MessageTest__foo extends tubepress_http_impl_puzzle_AbstractMessage
{
    
}
