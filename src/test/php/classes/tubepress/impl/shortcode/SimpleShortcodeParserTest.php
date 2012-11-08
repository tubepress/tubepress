<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_shortcode_SimpleShortcodeParserTest extends TubePressUnitTest
{
    private $_sut;
    private $_mockExecutionContext;
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_shortcode_SimpleShortcodeParser();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('butters');
    }


    public function testNothingToParse()
    {
        $shortcode = '[bu mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testMixedCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE                => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR                          => 'false',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP                => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE              => 3
        );

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testNoCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]';


        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testNoCustomOptions()
    {
        $shortcode = '[butters]';

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->never();

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testWeirdSingleQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]';

        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testWeirdDoubleQuotes()
    {
        $shortcode = '[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testNoQuotes()
    {
        $shortcode = '[butters mode=playlist	]';
        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testSingleQuotes()
    {
        $shortcode = '[butters mode=\'playlist\']';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testDoubleQuotes()
    {
        $shortcode = '[butters mode="playlist"]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testMismatchedStartEndQuotes()
    {
        $this->_sut->parse('[butters mode=\'playlist"]');

        $this->assertTrue(true);
    }

    function testNoClosingBracket()
    {
        $this->_sut->parse('[butters mode=\'playlist\'');

        $this->assertTrue(true);
    }

    function testNoOpeningBracket()
    {
        $content = "butters mode='playlist']";

        $this->_sut->parse($content);

        $this->assertTrue(true);
    }

    function testSpaceAroundAttributes()
    {
        $shortcode = "[butters mode='playlist']";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testSpaceAroundShortcode()
    {
        $shortcode = "sddf	 [butters mode='playlist']	sdsdfsdf";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    function testNoSpaceAroundShortcode()
    {
        $shortcode = "sddf[butters mode='playlist']sdsdfsdf";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    private function _setupExpectedFilters($expected)
    {
        $pm = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        foreach ($expected as $name => $value) {

            $pm->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT, Mockery::on(function ($arg) use ($name) {

                return $arg instanceof tubepress_api_event_TubePressEvent  && $arg->getArgument('optionName') === $name;
            }));
        }
    }
}

