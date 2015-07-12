<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener
 */
class tubepress_test_vimeo2_impl_listeners_options_VimeoOptionsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockUrlFactory  = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockStringUtils = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);
        $this->_mockEvent       = $this->mock('tubepress_lib_api_event_EventInterface');

        $realUrlFactory = new tubepress_url_impl_puzzle_UrlFactory();
        $this->_mockUrlFactory->shouldReceive('fromString')->atLeast(1)->andReturnUsing(array($realUrlFactory, 'fromString'));

        $realStringUtils = new tubepress_util_impl_StringUtils();
        $this->_mockStringUtils->shouldReceive('endsWith')->andReturnUsing(array($realStringUtils, 'endsWith'));
        $this->_mockStringUtils->shouldReceive('startsWith')->andReturnUsing(array($realStringUtils, 'startsWith'));
        $this->_mockStringUtils->shouldReceive('replaceFirst')->andReturnUsing(array($realStringUtils, 'replaceFirst'));

        $this->_sut = new tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener(

            $this->_mockUrlFactory,
            $this->_mockStringUtils
        );
    }

    /**
     * @dataProvider getDataGroup
     */
    public function testGroup($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onGroupValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataGroup()
    {
        return array(

            array('https://vimeo.com/groups/3012705', '3012705'),
            array('https://vimeo.com/x/3012705', 'https://vimeo.com/x/3012705'),
            array('http://www.youtube.com/watch?v=-wtIMTCHWuI', 'http://www.youtube.com/watch?v=-wtIMTCHWuI'),
            array(array(),                                      array()),
        );
    }

    /**
     * @dataProvider getDataChannel
     */
    public function testChannel($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onChannelValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataChannel()
    {
        return array(

            array('https://vimeo.com/channels/3012705', '3012705'),
            array('https://vimeo.com/x/3012705', 'https://vimeo.com/x/3012705'),
            array('http://www.youtube.com/watch?v=-wtIMTCHWuI', 'http://www.youtube.com/watch?v=-wtIMTCHWuI'),
            array(array(),                                      array()),
        );
    }

    /**
     * @dataProvider getDataAlbum
     */
    public function testAlbum($incoming, $expected)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);
        $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $expected);
        $this->_sut->onAlbumValue($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function getDataAlbum()
    {
        return array(

            array('https://vimeo.com/album/3012705', '3012705'),
            array('https://vimeo.com/x/3012705', 'https://vimeo.com/x/3012705'),
            array('http://www.youtube.com/watch?v=-wtIMTCHWuI', 'http://www.youtube.com/watch?v=-wtIMTCHWuI'),
            array(array(),                                      array()),
        );
    }
}