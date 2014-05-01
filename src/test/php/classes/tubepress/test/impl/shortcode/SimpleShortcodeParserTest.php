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
 * @covers tubepress_impl_shortcode_SimpleShortcodeParser<extended>
 */
class tubepress_test_impl_shortcode_SimpleShortcodeParserTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_shortcode_SimpleShortcodeParser
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

    public function onSetup()
    {
        $this->_mockExecutionContext = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('butters');
        $this->_sut = new tubepress_impl_shortcode_SimpleShortcodeParser($this->_mockExecutionContext);
    }


    public function testNothingToParse()
    {
        $shortcode = '[bu mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $this->_sut->parse($shortcode);

        $this->assertTrue(true);
    }

    public function testMixedCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE                => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR                          => 'false',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP                => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE              => 3
        );

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]';


        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoCustomOptions()
    {
        $shortcode = '[butters]';

        $this->_mockExecutionContext->shouldReceive('setAll')->never();

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testWeirdSingleQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]';

        $expected = array(
            tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testWeirdDoubleQuotes()
    {
        $shortcode = '[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoQuotes()
    {
        $shortcode = '[butters mode=playlist    ]';
        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);


        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testSingleQuotes()
    {
        $shortcode = '[butters mode=\'playlist\']';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testDoubleQuotes()
    {
        $shortcode = '[butters mode="playlist"]';

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testMismatchedStartEndQuotes()
    {
        $this->_sut->parse('[butters mode=\'playlist"]');

        $this->assertTrue(true);
    }

    public function testNoClosingBracket()
    {
        $this->_sut->parse('[butters mode=\'playlist\'');

        $this->assertTrue(true);
    }

    public function testNoOpeningBracket()
    {
        $content = "butters mode='playlist']";

        $this->_sut->parse($content);

        $this->assertTrue(true);
    }

    public function testSpaceAroundAttributes()
    {
        $shortcode = "[butters mode='playlist']";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters( $expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);
        $this->assertEquals($shortcode, $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testSpaceAroundShortcode()
    {
        $shortcode = "sddf     [butters mode='playlist']    sdsdfsdf";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertEquals("[butters mode='playlist']", $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    public function testNoSpaceAroundShortcode()
    {
        $shortcode = "sddf[butters mode='playlist']sdsdfsdf";

        $expected = array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($expected);

        $this->_mockExecutionContext->shouldReceive('setAll')->once()->with($expected);

        $this->_sut->parse($shortcode);

        $this->assertEquals("[butters mode='playlist']", $this->_sut->getLastShortcodeUsed());

        $this->assertTrue(true);
    }

    private function _setupExpectedFilters($expected)
    {
        $pm = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        foreach ($expected as $name => $value) {

            $pm->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_ANY_READ_FROM_EXTERNAL_INPUT, ehough_mockery_Mockery::on(function ($arg) use ($name) {

                return $arg instanceof tubepress_api_event_EventInterface  && $arg->getArgument('optionName') === $name;
            }));

            $pm->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTION_SINGLE_READ_FROM_EXTERNAL_INPUT . ".$name", ehough_mockery_Mockery::on(function ($arg) use ($name) {

                return $arg instanceof tubepress_api_event_EventInterface;
            }));
        }
    }
}

