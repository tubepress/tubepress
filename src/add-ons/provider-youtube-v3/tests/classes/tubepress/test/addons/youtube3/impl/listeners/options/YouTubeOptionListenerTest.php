<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_youtube3_impl_listeners_options_YouTubeOptionListener
 */
class tubepress_test_youtube3_impl_listeners_options_YouTubeOptionListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_youtube3_impl_listeners_options_YouTubeOptionListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockUrlFactory  = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockStringUtils = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockEvent       = $this->mock('tubepress_api_event_EventInterface');

        $realUrlFactory = new tubepress_url_impl_puzzle_UrlFactory();
        $this->_mockUrlFactory->shouldReceive('fromString')->atLeast(1)->andReturnUsing(array($realUrlFactory, 'fromString'));

        $realStringUtils = new tubepress_util_impl_StringUtils();
        $this->_mockStringUtils->shouldReceive('endsWith')->andReturnUsing(array($realStringUtils, 'endsWith'));
        $this->_mockStringUtils->shouldReceive('startsWith')->andReturnUsing(array($realStringUtils, 'startsWith'));
        $this->_mockStringUtils->shouldReceive('replaceFirst')->andReturnUsing(array($realStringUtils, 'replaceFirst'));

        $this->_sut = new tubepress_youtube3_impl_listeners_options_YouTubeOptionListener(

            $this->_mockUrlFactory,
            $this->_mockStringUtils
        );
    }

    /**
     * @dataProvider getDataList
     */
    public function testList($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onListValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataList()
    {
        return array(

            array('http://www.youtube.com/watch?v=-wtIMTCHWuI  ,  22837452321, cafecafe123', '-wtIMTCHWuI,22837452321,cafecafe123'),
            array("\t\t\nhttp://youtube.com/embed/-wtIMTCHWuI  , \n   http://youtu.be/22837452321\t,\tcafecafe123", '-wtIMTCHWuI,22837452321,cafecafe123'),
            array(array(),                                      array()),
        );
    }

    /**
     * @dataProvider getDataRelated
     */
    public function testRelatedTo($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onRelatedToValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataRelated()
    {
        return array(

            array('http://www.youtube.com/watch?v=-wtIMTCHWuI', '-wtIMTCHWuI'),
            array('http://www.youtube.com/v/-wtIMTCHWuI?version=3&autohide=1', '-wtIMTCHWuI'),
            array('http://youtu.be/-wtIMTCHWuI', '-wtIMTCHWuI'),
            array('http://youtube.com/embed/-wtIMTCHWuI', '-wtIMTCHWuI'),
            array('http://youtube.com/x/blabla/yo',             'http://youtube.com/x/blabla/yo'),
            array('http://vimeo.com/channel/blabla/yo',         'http://vimeo.com/channel/blabla/yo'),
            array(array(),                                      array()),
        );
    }

    /**
     * @dataProvider getDataUser
     */
    public function testUser($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onUserOrFavoritesValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataUser()
    {
        return array(

            array('http://youtube.com/user/foobar',       'foobar'),
            array('http://youtube.com/channel/blabla',    'blabla'),
            array('http://youtube.com/user/foobar/hi',    'foobar'),
            array('http://youtube.com/channel/blabla/yo', 'blabla'),
            array('http://youtube.com/x/blabla/yo',       'http://youtube.com/x/blabla/yo'),
            array('http://vimeo.com/channel/blabla/yo',   'http://vimeo.com/channel/blabla/yo'),
            array(array(),                                array()),
        );
    }

    /**
     * @dataProvider getDataPlaylist
     */
    public function testPlaylist($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onPlaylistValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataPlaylist()
    {
        return array(

            array('http://youtube.com/?list=PL123',  '123'),
            array('http://youtube.com/?f=b&p=PL123', '123'),
            array('http://youtube.com/?lt=123',      'http://youtube.com/?lt=123'),
            array('http://vimeo.com/?list=123',      'http://vimeo.com/?list=123'),
            array(array('hello'),                    array('hello')),
            array('hello',                           'hello'),
            array('PLhelloPL',                       'helloPL'),
        );
    }
}
