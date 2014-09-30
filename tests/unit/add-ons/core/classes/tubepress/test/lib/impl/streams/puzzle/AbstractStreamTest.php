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

abstract class tubepress_test_lib_impl_streams_puzzle_AbstractStreamTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDelegate;

    /**
     * @var tubepress_lib_impl_streams_puzzle_PuzzleBasedStream
     */
    private $_sut;

    public final function onSetup()
    {
        $this->_mockDelegate = $this->mock($this->getExpectedDelegateClass());
        $sutClass            = $this->getSutClass();
        $this->_sut  = new $sutClass($this->_mockDelegate);
    }

    protected abstract function getExpectedDelegateClass();

    protected abstract function getSutClass();

    public function testDetach()
    {
        $this->_mockDelegate->shouldReceive('detach')->once();
        $this->_sut->detach();
        $this->assertTrue(true);
    }

    public function testClose()
    {
        $this->_mockDelegate->shouldReceive('close')->once();
        $this->_sut->close();
        $this->assertTrue(true);
    }

    public function testEof()
    {
        $this->_mockDelegate->shouldReceive('eof')->once()->andReturn(true);
        $this->assertTrue($this->_sut->eof());
    }

    public function testGetContents()
    {
        $this->_mockDelegate->shouldReceive('getContents')->once()->with(33)->andReturn('foobar');
        $this->assertEquals('foobar', $this->_sut->getContents(33));
    }

    public function testGetSize()
    {
        $this->_mockDelegate->shouldReceive('getSize')->once()->andReturn(999);
        $this->assertEquals(999, $this->_sut->getSize());
    }

    public function testIsReadable()
    {
        $this->_mockDelegate->shouldReceive('isReadable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isReadable());
    }

    public function testIsSeekable()
    {
        $this->_mockDelegate->shouldReceive('isSeekable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isSeekable());
    }

    public function testIsWritable()
    {
        $this->_mockDelegate->shouldReceive('isWritable')->once()->andReturn(false);
        $this->assertFalse($this->_sut->isWritable());
    }

    public function testRead()
    {
        $this->_mockDelegate->shouldReceive('read')->once()->with(44)->andReturn('xyz');
        $this->assertEquals('xyz', $this->_sut->read(44));
    }

    public function testSeek()
    {
        $this->_mockDelegate->shouldReceive('seek')->once()->with(32, SEEK_END)->andReturn(true);
        $this->assertTrue($this->_sut->seek(32, SEEK_END));
    }

    public function testTell()
    {
        $this->_mockDelegate->shouldReceive('tell')->once()->andReturn(21);
        $this->assertEquals(21, $this->_sut->tell());
    }

    public function testWrite()
    {
        $this->_mockDelegate->shouldReceive('write')->once()->with('foo')->andReturn(6);
        $this->assertEquals(6, $this->_sut->write('foo'));
    }

    public function testToString()
    {
        $this->_mockDelegate->shouldReceive('__toString')->once()->andReturn('abc');
        $this->assertEquals('abc', $this->_sut->__toString());
    }
}