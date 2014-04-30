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
 * @covers tubepress_addons_puzzle_impl_stream_FlexibleStream<extended>
 */
class tubepress_test_addons_puzzle_impl_stream_FlexibleStreamTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mock;

    /**
     * @var tubepress_addons_puzzle_impl_stream_FlexibleStream
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mock = ehough_mockery_Mockery::mock('tubepress_api_stream_StreamInterface');
        $this->_sut  = new tubepress_addons_puzzle_impl_stream_FlexibleStream($this->_mock);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tubepress_addons_puzzle_impl_stream_FlexibleStream must be constructed with instance of either puzzle_stream_StreamInterface or tubepress_api_stream_StreamInterface
     *
     */
    public function testNonStreamArg()
    {
        new tubepress_addons_puzzle_impl_stream_FlexibleStream(array('foo'));
    }

    public function testDetach()
    {
        $this->_mock->shouldReceive('detach')->once();
        $this->_sut->detach();
        $this->assertTrue(true);
    }

    public function testClose()
    {
        $this->_mock->shouldReceive('close')->once();
        $this->_sut->close();
        $this->assertTrue(true);
    }

    public function testEof()
    {
        $this->_mock->shouldReceive('eof')->once()->andReturn(true);
        $this->assertTrue($this->_sut->eof());
    }

    public function testGetContents()
    {
        $this->_mock->shouldReceive('getContents')->once()->with(33)->andReturn('foobar');
        $this->assertEquals('foobar', $this->_sut->getContents(33));
    }

    public function testGetSize()
    {
        $this->_mock->shouldReceive('getSize')->once()->andReturn(999);
        $this->assertEquals(999, $this->_sut->getSize());
    }

    public function testIsReadable()
    {
        $this->_mock->shouldReceive('isReadable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isReadable());
    }

    public function testIsSeekable()
    {
        $this->_mock->shouldReceive('isSeekable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isSeekable());
    }

    public function testIsWritable()
    {
        $this->_mock->shouldReceive('isWritable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isWritable());
    }

    public function testRead()
    {
        $this->_mock->shouldReceive('read')->once()->with(44)->andReturn('xyz');
        $this->assertEquals('xyz', $this->_sut->read(44));
    }

    public function testSeek()
    {
        $this->_mock->shouldReceive('seek')->once()->with(32, SEEK_END)->andReturn(true);
        $this->assertTrue($this->_sut->seek(32, SEEK_END));
    }

    public function testTell()
    {
        $this->_mock->shouldReceive('tell')->once()->andReturn(21);
        $this->assertEquals(21, $this->_sut->tell());
    }

    public function testWrite()
    {
        $this->_mock->shouldReceive('write')->once()->with('foo')->andReturn(6);
        $this->assertEquals(6, $this->_sut->write('foo'));
    }

    public function testToString()
    {
        $this->_mock->shouldReceive('__toString')->twice()->andReturn('abc');
        $this->assertEquals('abc', $this->_sut->toString());
        $this->assertEquals('abc', $this->_sut->__toString());
    }
}