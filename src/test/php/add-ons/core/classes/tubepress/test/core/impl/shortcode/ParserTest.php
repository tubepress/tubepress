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
 * @covers tubepress_core_impl_shortcode_Parser<extended>
 */
class tubepress_test_impl_shortcode_ParserTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_shortcode_Parser
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_mockStringUtils      = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::KEYWORD)->andReturn('butters');
        $this->_sut = new tubepress_core_impl_shortcode_Parser($this->_mockLogger, $this->_mockExecutionContext, $this->_mockEventDispatcher, $this->_mockStringUtils);
    }


    public function testNothingToParse()
    {
        $shortcode = '[bu mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    public function testMixedCommasWithAllSortsOfQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);
        $shortcode = '[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE                => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_core_api_const_options_Names::AUTHOR                          => 'false',
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP                => 200,
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE              => 3
        );

        $this->_setupExpectedFilters($expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoCommasWithAllSortsOfQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_core_api_const_options_Names::AUTHOR => 'true',
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP => 200,
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testCommasWithAllSortsOfQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]';


        $expected = array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_core_api_const_options_Names::AUTHOR => 'true',
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP => 200,
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($expected, $shortcode);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoCustomOptions()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters]';

        $this->_mockExecutionContext->shouldReceive('setAll')->never();

        $this->_mockStringUtils->shouldReceive('redactSecrets')->twice()->with($shortcode);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testWeirdSingleQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]';

        $expected = array(
            tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected, $shortcode);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testWeirdDoubleQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]';

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST,
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=playlist    ]';
        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected, $shortcode);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testSingleQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode=\'playlist\']';

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testDoubleQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = '[butters mode="playlist"]';

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testMismatchedStartEndQuotes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_mockStringUtils->shouldReceive('redactSecrets')->once()->with('[butters mode=\'playlist"]')->andReturn('abc');
        $this->_sut->parse('[butters mode=\'playlist"]');

        $this->assertTrue(true);
    }

    public function testNoClosingBracket()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut->parse('[butters mode=\'playlist\'');

        $this->assertTrue(true);
    }

    public function testNoOpeningBracket()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $content = "butters mode='playlist']";

        $this->_sut->parse($content);

        $this->assertTrue(true);
    }

    public function testSpaceAroundAttributes()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = "[butters mode='playlist']";

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected, $shortcode);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testSpaceAroundShortcode()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = "sddf     [butters mode='playlist']    sdsdfsdf";

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected, "[butters mode='playlist']");

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertEquals("[butters mode='playlist']", $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoSpaceAroundShortcode()
    {
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $shortcode = "sddf[butters mode='playlist']sdsdfsdf";

        $expected = array(tubepress_core_api_const_options_Names::GALLERY_SOURCE => tubepress_youtube_api_const_options_Values::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected, "[butters mode='playlist']");

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertEquals("[butters mode='playlist']", $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    private function _setupExpectedFilters($expected, $shortcode)
    {
        foreach ($expected as $name => $value) {

            $mockAnyEvent = $this->mock('tubepress_core_api_event_EventInterface');
            $mockAnyEvent->shouldReceive('getSubject')->once()->andReturn('abc');
            $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($value, array('optionName' => $name))->andReturn($mockAnyEvent);
            $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, $mockAnyEvent);

            $mockSingleEvent = $this->mock('tubepress_core_api_event_EventInterface');
            $mockSingleEvent->shouldReceive('getSubject')->once()->andReturn($value);
            $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('abc')->andReturn($mockSingleEvent);
            $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . ".$name", $mockSingleEvent);

            $this->_mockStringUtils->shouldReceive('redactSecrets')->twice()->with($value);
        }

        $this->_mockStringUtils->shouldReceive('redactSecrets')->twice()->with($shortcode);
    }
}

